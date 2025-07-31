<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // User yang melakukan aksi
            $table->string('action'); // created, confirmed, cancelled, updated
            $table->text('description')->nullable(); // Deskripsi perubahan
            $table->json('old_values')->nullable(); // Nilai lama (untuk update)
            $table->json('new_values')->nullable(); // Nilai baru (untuk update)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
    }
};