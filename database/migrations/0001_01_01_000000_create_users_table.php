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
      $table->string('name');
      $table->string('username')->unique();
      $table->string('password');
      $table->boolean('two_factor_enabled')->default(false);
      $table->text('google2fa_secret')->nullable();
      $table->enum('status', ['active', 'inactive'])->default('active');
      $table->rememberToken();
      $table->timestamps();
      $table->softDeletes();
      $table->foreignId('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignId('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
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
    Schema::dropIfExists('sessions');
  }
};
