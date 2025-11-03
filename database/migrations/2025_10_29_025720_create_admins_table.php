<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('admin_username', 30)->unique();
            $table->string('admin_password', 100);
            $table->string('admin_nama', 50);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('admins');
    }
};
