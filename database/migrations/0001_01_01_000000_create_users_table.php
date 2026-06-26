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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // This acts as your user_id (Primary Key, Auto Increment)

            $table->string('name'); // This acts as your username (VARCHAR)
            $table->string('email')->unique(); // (VARCHAR, Unique)
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password'); // This stores the password_hash (VARCHAR)

            // ADD THIS LINE: The role column defaulting to 'user'
            $table->string('role')->default('user');

            $table->rememberToken();
            $table->timestamps(); // This automatically creates 'created_at' and 'updated_at' timestamps
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
