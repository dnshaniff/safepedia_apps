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
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->string('proforma_number');
      $table->string('invoice_number')->nullable();
      $table->string('customer_name');
      $table->string('customer_address');
      $table->string('customer_phone')->nullable();
      $table->enum('payment_terms', ['cbd', 'cod', 'dp']);
      $table->string('reference');
      $table->date('issued_date');
      $table->date('valid_until');
      $table->decimal('subtotal', 15, 2)->default(0);
      $table->decimal('discount_total', 15, 2)->default(0);
      $table->decimal('grand_total', 15, 2)->default(0);
      $table->decimal('paid_amount', 15, 2)->default(0);
      $table->decimal('remaining_amount', 15, 2)->default(0);
      $table->text('notes')->nullable();
      $table->timestamps();
      $table->softDeletes();
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
    Schema::dropIfExists('invoices');
  }
};
