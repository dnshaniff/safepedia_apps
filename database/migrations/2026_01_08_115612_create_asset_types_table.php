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
    Schema::create('asset_types', function (Blueprint $table) {
      $table->id();
      $table->foreignId('asset_category_id')->nullable()->constrained('asset_categories', 'id')->onDelete('SET NULL');
      $table->string('type_name');
      $table->string('type_code')->unique();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->onDelete('SET NULL');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('asset_types');
  }
};
