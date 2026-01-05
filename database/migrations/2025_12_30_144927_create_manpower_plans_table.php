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
    Schema::create('manpower_plans', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignId('org_unit_id')->nullable()->constrained('org_units', 'id')->onDelete('SET NULL');
      $table->string('position_title');
      $table->date('planned_date');
      $table->integer('number_positions')->default(0);
      $table->string('devices');
      $table->text('notes')->nullable();
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
    Schema::dropIfExists('manpower_plans');
  }
};
