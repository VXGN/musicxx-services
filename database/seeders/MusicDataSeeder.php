<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Song;

class MusicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Artists
        $wisp = Artist::firstOrCreate(
            ['name' => 'Wisp'],
            ['bio' => 'Nu Gaze and alternative rock artist']
        );

        $hoyo = Artist::firstOrCreate(
            ['name' => 'HOYO-MiX'],
            ['bio' => 'Genshin Impact soundtrack composer']
        );

        // Create Albums
        $pandoraAlbum = Album::firstOrCreate(
            ['title' => 'Pandora', 'artist_id' => $wisp->id],
            ['cover_image' => null]
        );

        $sumeruAlbum = Album::firstOrCreate(
            ['title' => 'Genshin Impact - The Unfathomable Sand Dunes (Original Game Soundtrack)', 'artist_id' => $hoyo->id],
            ['cover_image' => null]
        );

        $forestAlbum = Album::firstOrCreate(
            ['title' => 'Genshin Impact - Forest of Jnana and Vidya (Original)', 'artist_id' => $hoyo->id],
            ['cover_image' => null]
        );

        // Create Songs
        Song::firstOrCreate(
            ['title' => 'Pandora', 'artist_id' => $wisp->id],
            [
                'album_id' => $pandoraAlbum->id,
                'duration' => null,
                'file_url' => 'https://sfwyfiymjuyvbjdhmgji.supabase.co/storage/v1/object/sign/Music/Pandora.mp3?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8yMWJhN2ZjNS1iMGM1LTQxN2MtYjUxMi1lYjAyYzkzNGI2NTMiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJNdXNpYy9QYW5kb3JhLm1wMyIsImlhdCI6MTc2Nzk2NjE0MSwiZXhwIjoxNzk5NTAyMTQxfQ.VCCvSjxGTdcxTf6ooiq5gKxxLKVr3aCKHCyHD61iSA4'
            ]
        );

        Song::firstOrCreate(
            ['title' => 'Sumeru', 'artist_id' => $hoyo->id],
            [
                'album_id' => $sumeruAlbum->id,
                'duration' => null,
                'file_url' => 'https://sfwyfiymjuyvbjdhmgji.supabase.co/storage/v1/object/sign/Music/Sumeru.mp3?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8yMWJhN2ZjNS1iMGM1LTQxN2MtYjUxMi1lYjAyYzkzNGI2NTMiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJNdXNpYy9TdW1lcnUubXAzIiwiaWF0IjoxNzY3OTY2MTczLCJleHAiOjE3OTk1MDIxNzN9.pJAOfFIjYIXjSVTEn1znavLiaEmOkHqjWbPleUGXGBw'
            ]
        );

        Song::firstOrCreate(
            ['title' => 'Swirl of Shamshir', 'artist_id' => $hoyo->id],
            [
                'album_id' => $forestAlbum->id,
                'duration' => null,
                'file_url' => 'https://sfwyfiymjuyvbjdhmgji.supabase.co/storage/v1/object/sign/Music/Swirls%20of%20Shamshir.mp3?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8yMWJhN2ZjNS1iMGM1LTQxN2MtYjUxMi1lYjAyYzkzNGI2NTMiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJNdXNpYy9Td2lybHMgb2YgU2hhbXNoaXIubXAzIiwiaWF0IjoxNzY3OTY2MTgxLCJleHAiOjE3OTk1MDIxODF9.0Zm98Qft7oD1S8BaOskDsL4CHkXIgNEgoyDc15JHVRY'
            ]
        );

        Song::firstOrCreate(
            ['title' => 'Your Face', 'artist_id' => $wisp->id],
            [
                'album_id' => $pandoraAlbum->id,
                'duration' => null,
                'file_url' => 'https://sfwyfiymjuyvbjdhmgji.supabase.co/storage/v1/object/sign/Music/Your%20Face.mp3?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8yMWJhN2ZjNS1iMGM1LTQxN2MtYjUxMi1lYjAyYzkzNGI2NTMiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJNdXNpYy9Zb3VyIEZhY2UubXAzIiwiaWF0IjoxNzY3OTY2MTg3LCJleHAiOjE3OTk1MDIxODd9.VJ90nI4HbbaQJy4ZQGlQM2O-OI0yofcnfWHhTkwqXtY'
            ]
        );
    }
}
