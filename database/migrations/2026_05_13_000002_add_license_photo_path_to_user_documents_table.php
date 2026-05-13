<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->string('license_photo_path')->nullable()->after('valid_id_path');
        });
    }

    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropColumn('license_photo_path');
        });
    }
};