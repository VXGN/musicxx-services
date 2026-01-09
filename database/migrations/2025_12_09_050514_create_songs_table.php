<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            // Relasi ke artist
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');

            // Optional relasi ke album
            $table->foreignId('album_id')->nullable()->constrained('albums')->onDelete('set null');

            $table->integer('duration')->nullable();   // detik
            $table->text('file_url')->nullable();   // URL ke file audio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
