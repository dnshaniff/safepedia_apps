<?php

namespace App\Imports;

use App\Models\JobTitle;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JobTitlesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
  protected string $userId;
  protected int $inserted = 0;

  public function __construct(string $userId)
  {
    $this->userId = $userId;
  }

  public function model(array $row)
  {
    $this->inserted++;

    return new JobTitle([
      'title_name' => trim($row['title_name']),
      'created_by' => $this->userId,
    ]);
  }

  public function rules(): array
  {
    return [
      '*.title_name' => 'required|string|max:255|unique:job_titles,title_name',
    ];
  }

  public function getInsertedCount(): int
  {
    return $this->inserted;
  }
}
