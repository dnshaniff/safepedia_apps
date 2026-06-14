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
    Schema::create('approvals', function (Blueprint $table) {
      $table->id();
      $table->unsignedTinyInteger('sequence');
      $table->string('approval_role');
      $table->foreignId('employee_id')->nullable()->constrained('employees', 'id')->nullOnDelete();
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
    Schema::dropIfExists('approvals');
  }
};
