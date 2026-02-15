<?php

namespace App\Imports;

use Exception;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\OrgUnit;
use App\Models\Employee;
use App\Models\JobTitle;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithChunkReading, WithBatchInserts, WithValidation
{
  protected string $userId;
  protected $companies;
  protected $orgUnits;
  protected $jobTitles;
  protected int $inserted = 0;

  public function __construct(string $userId)
  {
    $this->userId = $userId;

    $this->companies = Company::pluck('id', 'company_code');
    $this->orgUnits = OrgUnit::pluck('id', 'unit_code');
    $this->jobTitles = JobTitle::pluck('id', 'title_name');
  }

  public function prepareForValidation($data, $index)
  {
    $data['employee_code'] = isset($data['employee_code'])
      ? ltrim(trim($data['employee_code']), "'")
      : null;

    $data['phone_number'] = isset($data['phone_number'])
      ? preg_replace('/\s+/', '', $data['phone_number'])
      : null;

    return $data;
  }

  public function model(array $row)
  {
    $employee = new Employee([
      'id' => (string) Str::uuid(),
      'employee_code' => $row['employee_code'],
      'full_name' => $row['full_name'],
      'join_date' => $this->parseDate($row['join_date']),
      'company_id' => $this->companies->get($row['company']),
      'org_unit_id' => $this->orgUnits->get($row['organization']),
      'job_title_id' => $this->jobTitles->get($row['job_title']),
      'employment_status' => $row['employment_status'],
      'office_email' => $row['office_email'],
      'personal_email' => $row['personal_email'],
      'phone_number' => $row['phone_number'],
      'gender' => $row['gender'],
      'date_of_birth' => $this->parseDate($row['date_of_birth']),
      'ktp_number' => $row['ktp_number'],
      'created_by' => $this->userId,
    ]);

    $this->inserted++;

    return $employee;
  }

  public function rules(): array
  {
    return [
      '*.employee_code' => ['required', 'distinct', 'unique:employees,employee_code'],
      '*.full_name' => ['required'],
      '*.join_date' => ['required'],
      '*.company' => ['required', Rule::exists('companies', 'company_code')],
      '*.organization' => ['required', Rule::exists('org_units', 'unit_code')],
      '*.job_title' => ['required', Rule::exists('job_titles', 'title_name')],
      '*.employment_status' => [
        'required',
        Rule::in(['Colleague', 'Contract', 'Freelance', 'Intern', 'Probation', 'Resign'])
      ],
      '*.office_email' => ['required', 'email', 'distinct', 'unique:employees,office_email'],
      '*.personal_email' => ['required', 'email', 'distinct', 'unique:employees,personal_email'],
      '*.phone_number' => ['required', 'distinct', 'unique:employees,phone_number'],
      '*.gender' => ['required', Rule::in(['Male', 'Female'])],
      '*.date_of_birth' => ['required'],
      '*.ktp_number' => ['required', 'distinct', 'unique:employees,ktp_number'],
    ];
  }

  public function batchSize(): int
  {
    return 100;
  }

  public function chunkSize(): int
  {
    return 100;
  }

  public function getInsertedCount(): int
  {
    return $this->inserted;
  }

  protected function parseDate($value)
  {
    if (!$value) {
      return null;
    }

    try {
      if (is_numeric($value)) {
        return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
      }

      return Carbon::parse($value)->format('Y-m-d');
    } catch (Exception $e) {
      return null;
    }
  }
}
