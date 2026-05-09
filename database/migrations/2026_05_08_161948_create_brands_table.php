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
    Schema::create('brands', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('file_name')->nullable();
      $table->string('file_path')->nullable();
      $table->string('file_mime')->nullable();
      $table->unsignedBigInteger('file_size')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('brands');
  }
};
