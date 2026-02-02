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
    Schema::create('new_comers', function (Blueprint $table) {
      $table->id();
      $table->foreignId('ta_candidate_id')->constrained('ta_candidates', 'id')->onDelete('cascade');
      $table->date('join_date');
      $table->date('end_of_contract_date')->nullable();
      $table->enum('working_status', ['Contract', 'Freelance', 'Full Time', 'Internship'])->default('Contract');
      $table->foreignUuid('manager_id')->nullable()->constrained('employees', 'id')->onDelete('set null');
      $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('set null');
      $table->string('email');
      $table->string('phone_number');
      $table->enum('status_join', ['Planned', 'Joined', 'No Show', 'Canceled'])->default('Planned');
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
    Schema::dropIfExists('new_comers');
  }
};
