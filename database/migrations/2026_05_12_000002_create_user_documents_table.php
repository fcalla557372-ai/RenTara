<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create user_documents table to store identification documents
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('driver_license_no')->unique();
            $table->date('license_expiry');
            $table->string('national_id_no')->unique();
            $table->string('id_type');
            $table->string('valid_id_path')->nullable();
            $table->string('birth_cert_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
