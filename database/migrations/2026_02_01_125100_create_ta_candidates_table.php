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
    Schema::create('ta_candidates', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('manpower_plan_id')->constrained('manpower_plans', 'id')->onDelete('cascade');
      $table->string('full_name');
      $table->enum('gender', ['Female', 'Male']);
      $table->string('email')->nullable();
      $table->string('phone_number')->nullable();
      $table->enum('interview_status', ['Screening', 'Interview', 'Offering', 'Offer Accepted', 'Canceled'])->default('Screening');
      $table->date('expected_join_date')->nullable();
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
    Schema::dropIfExists('ta_candidates');
  }
};
