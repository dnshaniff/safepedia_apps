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
    Schema::create('manpower_plan_asset_type', function (Blueprint $table) {
      $table->foreignUuid('manpower_plan_id')->constrained('manpower_plans', 'id')->onDelete('cascade');
      $table->foreignId('asset_type_id')->constrained('asset_types', 'id')->onDelete('restrict');

      $table->primary(['manpower_plan_id', 'asset_type_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('manpower_plan_asset_type');
  }
};
