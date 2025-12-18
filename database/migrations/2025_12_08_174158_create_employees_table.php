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
    Schema::create('employees', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->nullable()->constrained('users', 'id')->onDelete('SET NULL');
      $table->string('employee_code')->unique();
      $table->string('full_name');
      $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('SET NULL');
      $table->foreignUuid('hrbp_id')->nullable()->constrained('employees', 'id')->onDelete('SET NULL');
      $table->foreignUuid('manager_id')->nullable()->constrained('employees', 'id')->onDelete('SET NULL');
      $table->date('join_date');
      $table->foreignId('job_title_id')->nullable()->constrained('job_titles', 'id')->onDelete('SET NULL');
      $table->foreignId('org_unit_id')->nullable()->constrained('org_units', 'id')->onDelete('SET NULL');
      $table->enum('employment_type', ['Colleague', 'Contract', 'Freelance', 'Intern', 'Probation', 'Resign']);
      $table->string('office_email')->nullable()->unique();
      $table->string('personal_email')->nullable()->unique();
      $table->string('phone_number')->nullable();
      $table->enum('gender', ['Female', 'Male']);
      $table->date('date_of_birth')->nullable();
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
    Schema::dropIfExists('employees');
  }
};
