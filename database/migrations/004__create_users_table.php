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
            $table->id();

            // Login info
            $table->string('email')->unique();
            $table->string('password');

            // User info
            $table->string('role_slug')->default('medewerker');
            $table->foreign('role_slug')->references('slug')->on('roles');

            $table->string('department_slug')->nullable();
            $table->foreign('department_slug')->references('slug')->on('departments');

            $table->string('subdepartment_slug')->nullable();
            $table->foreign('subdepartment_slug')->references('slug')->on('subdepartments');

            $table->foreignId('supervisor_id')->nullable()->constrained('users', 'id');

            // User status
            $table->boolean('verified')->default(false);
            $table->boolean('blocked')->default(false);

            // Personal information
            $table->string('first_name');
            $table->string('sure_name');
            $table->string('bsn', 9)->unique();
            $table->date('date_of_service');

            // Vacation days
            $table->float('used_attendance')->default(0);
            $table->float('max_attendance')->default(100);

            $table->rememberToken();
            $table->timestamps();
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
