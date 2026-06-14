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
    Schema::create('warehouse_constructions', function (Blueprint $table) {
      $table->id();
      $table->string('construction_number')->unique();
      $table->string('warehouse_name');
      $table->decimal('latitude', 10, 8);
      $table->decimal('longitude', 11, 8);
      $table->decimal('grand_total_budget', 18, 2)->default(0);
      $table->enum('status', ['draft', 'pending', 'approved', 'returned', 'canceled'])->default('draft');
      $table->timestamps();
      $table->softDeletes();
      $table->foreignId('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignId('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('warehouse_constructions');
  }
};
