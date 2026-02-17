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
    Schema::create('asset_locations', function (Blueprint $table) {
      $table->id();
      $table->string('location_name');
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
    Schema::dropIfExists('asset_locations');
  }
};
