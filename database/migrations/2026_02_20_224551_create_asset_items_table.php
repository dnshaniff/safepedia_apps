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
    Schema::create('asset_items', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignId('asset_type_id')->nullable()->constrained('asset_types', 'id')->onDelete('SET NULL');
      $table->string('item_code')->unique();
      $table->string('public_code')->unique();
      $table->string('item_brand')->nullable();
      $table->string('serial_number')->nullable();
      $table->string('item_model')->nullable();
      $table->text('item_specification')->nullable();
      $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('SET NULL');
      $table->enum('item_status', ['Active', 'In Repar', 'Disposed', 'Lost'])->default('Active');
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
    Schema::dropIfExists('asset_items');
  }
};
