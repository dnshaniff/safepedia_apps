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
    Schema::create('employee_agreements', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('employee_id')->constrained('employees', 'id')->onDelete('CASCADE');
      $table->enum('agreement_type', ['Contract', 'Conversion', 'Extension', 'Promotion', 'Resign', 'Warning']);
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->date('effective_date')->nullable();
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
    Schema::dropIfExists('employee_agreements');
  }
};
