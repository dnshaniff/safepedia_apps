<?php

namespace App\Domains\Products\Queries;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class IndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = Product::query()->with(['brand:id,name', 'thumbnail:id,product_id,file_path', 'creator', 'editor', 'deleter']);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = (clone $baseQuery)->count();

    $rows = $baseQuery->latest()->offset($start)->limit($length)->get();

    $data = [];
    $ids = $start;

    foreach ($rows as $row) {
      $data[] = [
        'fake_id' => ++$ids,
        'id' => $row->id,
        'name' => $row->name,
        'description' => $row->description,
        'brand' => $row->brand->name ?? '-',
        'thumbnail' => $row->thumbnail?->file_path ? asset('storage/' . $row->thumbnail->file_path) : null,
        'status' => $row->status,
        'creator' => $row->creator?->name ?? '-',
        'editor' => $row->editor?->name ?? '-',
        'deleter' => $row->deleter?->name ?? '-',
        'created_at' => $row->created_at,
        'updated_at' => $row->updated_at,
        'deleted_at' => $row->deleted_at,
      ];
    }

    return [
      'draw' => (int) ($params['draw'] ?? 1),
      'recordsTotal' => $totalData,
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
    ];
  }
}
