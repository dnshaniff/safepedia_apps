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
    Schema::create('warehouse_construction_documents', function (Blueprint $table) {
      $table->id();
      $table->foreignId('warehouse_construction_id')->constrained('warehouse_constructions', 'id')->cascadeOnDelete();
      $table->string('file_name');
      $table->string('file_path');
      $table->string('file_mime');
      $table->unsignedBigInteger('file_size');
      $table->string('original_name');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('warehouse_construction_documents');
  }
};
