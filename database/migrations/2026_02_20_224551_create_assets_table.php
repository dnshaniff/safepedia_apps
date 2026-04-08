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
    Schema::create('asset_categories', function (Blueprint $table) {
      $table->id();
      $table->string('category_code')->unique();
      $table->string('category_name');
      $table->boolean('is_active')->default(true);
      $table->softDeletes();
      $table->timestamps();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('asset_types', function (Blueprint $table) {
      $table->id();
      $table->foreignId('asset_category_id')->nullable()->constrained('asset_categories', 'id')->nullOnDelete();
      $table->string('type_name');
      $table->string('type_code')->unique();
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('asset_locations', function (Blueprint $table) {
      $table->id();
      $table->string('floor');
      $table->string('room')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('asset_items', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignId('asset_type_id')->nullable()->constrained('asset_types', 'id')->nullOnDelete();
      $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->nullOnDelete();
      $table->string('item_code')->unique();
      $table->string('public_code')->unique();
      $table->string('item_brand')->nullable();
      $table->string('item_model')->nullable();
      $table->string('serial_number')->nullable();
      $table->text('item_specification')->nullable();
      $table->enum('item_status', ['Active', 'Disposed', 'In Repair', 'Lost', 'Retired'])->default('Active');
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('asset_movements', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('asset_item_id')->constrained('asset_items', 'id')->cascadeOnDelete();
      $table->foreignUuid('employee_id')->nullable()->constrained('employees', 'id')->nullOnDelete();
      $table->foreignId('asset_location_id')->nullable()->constrained('asset_locations', 'id')->nullOnDelete();
      $table->date('start_date');
      $table->date('end_date')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
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
    Schema::dropIfExists('asset_movements');
    Schema::dropIfExists('asset_items');
    Schema::dropIfExists('asset_locations');
    Schema::dropIfExists('asset_types');
    Schema::dropIfExists('asset_categories');
  }
};
