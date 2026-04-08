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
      $table->foreignUuid('user_id')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->string('employee_code');
      $table->string('full_name');
      $table->enum('gender', ['Male', 'Female']);
      $table->date('date_of_birth')->nullable();
      $table->string('ktp_number')->nullable()->unique();
      $table->string('office_email')->nullable()->unique();
      $table->string('personal_email')->nullable();
      $table->string('phone_number')->nullable();
      $table->text('address')->nullable();
      $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->nullOnDelete();
      $table->date('join_date');
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('employee_employments', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('employee_id')->constrained('employees', 'id')->cascadeOnDelete();
      $table->enum('employment_status', ['Probation', 'Permanent', 'Contract', 'Intern', 'Freelance', 'Resigned', 'Terminated']);
      $table->date('start_date');
      $table->date('end_date')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('employee_job_histories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employment_id')->constrained('employee_employments', 'id')->cascadeOnDelete();
      $table->foreignId('job_title_id')->nullable()->constrained('job_titles', 'id')->nullOnDelete();
      $table->foreignId('org_unit_id')->nullable()->constrained('org_units', 'id')->nullOnDelete();
      $table->foreignUuid('manager_id')->nullable()->constrained('employees', 'id')->nullOnDelete();
      $table->date('start_date');
      $table->date('end_date')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('employee_agreements', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('employee_id')->constrained('employees', 'id')->cascadeOnDelete();
      $table->enum('agreement_type', ['Contract', 'Conversion', 'Extension', 'Promotion', 'Resignation', 'Warning']);
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->date('effective_date')->nullable();
      $table->text('notes')->nullable();
      $table->string('document_name')->nullable();
      $table->string('document_path')->nullable();
      $table->string('document_mime')->nullable();
      $table->foreignUuid('approved_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
      $table->softDeletes();
      $table->foreignUuid('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();
      $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'id')->nullOnDelete();
    });

    Schema::create('employee_documents', function (Blueprint $table) {
      $table->id();
      $table->foreignUuid('employee_id')->constrained('employees', 'id')->cascadeOnDelete();
      $table->string('document_type');
      $table->string('file_name');
      $table->string('file_path');
      $table->string('mime');
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
    Schema::dropIfExists('employee_documents');
    Schema::dropIfExists('employee_agreements');
    Schema::dropIfExists('employee_job_histories');
    Schema::dropIfExists('employee_employments');
    Schema::dropIfExists('employees');
  }
};
