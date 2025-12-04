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
    Schema::create('org_units', function (Blueprint $table) {
      $table->id();
      $table->foreignId('parent_id')->nullable()->constrained('org_units', 'id')->onDelete('SET NULL');
      $table->string('unit_name');
      $table->string('unit_code')->unique();
      $table->enum('unit_type', ['Department', 'Division', 'Office'])->default('Department');
      $table->integer('sort_order')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('org_units');
  }
};
