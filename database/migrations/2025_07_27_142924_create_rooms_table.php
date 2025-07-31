<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Kode ruangan: R001, R002, etc
            $table->text('description')->nullable();
            $table->integer('capacity'); // Kapasitas orang
            $table->string('location'); // Lokasi ruangan
            $table->string('floor')->nullable(); // Lantai
            $table->json('facilities')->nullable(); // TV, Projector, AC, etc
            $table->decimal('price_per_hour', 10, 2)->default(0); // Tarif per jam
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->string('image')->nullable(); // Foto ruangan
            $table->boolean('requires_approval')->default(false); // Butuh approval atau tidak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};