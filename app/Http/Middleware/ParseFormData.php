<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParseFormData
{
    /**
     * Handle PUT/PATCH requests with form-data by parsing the raw input.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['PUT', 'PATCH'])) {
            $contentType = $request->header('Content-Type', '');
            
            // Handle application/x-www-form-urlencoded
            if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                parse_str($request->getContent(), $data);
                $request->merge($data);
            }
            
            // Handle multipart/form-data
            if (str_contains($contentType, 'multipart/form-data')) {
                $this->parseMultipartFormData($request);
            }
        }

        return $next($request);
    }

    /**
     * Parse multipart form data for PUT/PATCH requests.
     */
    protected function parseMultipartFormData(Request $request): void
    {
        $content = $request->getContent();
        
        if (empty($content)) {
            return;
        }

        // Get boundary from content type
        preg_match('/boundary=(.*)$/', $request->header('Content-Type'), $matches);
        
        if (!isset($matches[1])) {
            return;
        }

        $boundary = $matches[1];
        $blocks = preg_split('/-+' . preg_quote($boundary, '/') . '/', $content);
        array_pop($blocks); // Remove last empty block
        array_shift($blocks); // Remove first empty block

        $data = [];
        $files = [];

        foreach ($blocks as $block) {
            if (empty(trim($block))) {
                continue;
            }

            // Split headers and content
            $parts = preg_split('/\r\n\r\n/', $block, 2);
            
            if (count($parts) !== 2) {
                continue;
            }

            $headers = $parts[0];
            $value = rtrim($parts[1], "\r\n");

            if (preg_match('/name="([^"]*)"/', $headers, $nameMatch)) {
                $name = $nameMatch[1];

                if (preg_match('/filename="([^"]*)"/', $headers, $filenameMatch)) {
                    $filename = $filenameMatch[1];
                    
                    if (!empty($filename)) {
                        preg_match('/Content-Type:\s*([^\r\n]+)/', $headers, $contentTypeMatch);
                        $mimeType = isset($contentTypeMatch[1]) ? trim($contentTypeMatch[1]) : 'application/octet-stream';

                        $tempPath = tempnam(sys_get_temp_dir(), 'upload_');
                        file_put_contents($tempPath, $value);

                        $files[$name] = new \Illuminate\Http\UploadedFile(
                            $tempPath,
                            $filename,
                            $mimeType,
                            null,
                            true
                        );
                    }
                } else {
                    $data[$name] = $value;
                }
            }
        }

        if (!empty($data)) {
            $request->merge($data);
        }

        if (!empty($files)) {
            $request->files->add($files);
        }
    }
}
