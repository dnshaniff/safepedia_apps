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
    Schema::create('warehouse_construction_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('warehouse_construction_id')->constrained('warehouse_constructions', 'id')->cascadeOnDelete();
      $table->string('item_name');
      $table->integer('quantity');
      $table->decimal('unit_price', 18, 2);
      $table->decimal('line_total', 18, 2);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('warehouse_construction_items');
  }
};
