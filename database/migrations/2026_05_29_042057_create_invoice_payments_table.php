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
    Schema::create('invoice_payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained('invoices', 'id')->cascadeOnDelete();
      $table->date('payment_date');
      $table->decimal('amount', 15, 2);
      $table->string('payment_method')->nullable();
      $table->text('notes')->nullable();
      $table->string('file_name')->nullable();
      $table->string('file_path')->nullable();
      $table->string('file_mime')->nullable();
      $table->unsignedBigInteger('file_size')->nullable();
      $table->timestamps();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoice_payments');
  }
};
