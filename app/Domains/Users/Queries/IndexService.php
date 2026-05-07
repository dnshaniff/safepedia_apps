<?php

namespace App\Domains\Users\Queries;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class IndexService
{
  public function execute(array $params): array
  {
    $user = Auth::user();

    $isAdmin = $user->username === 'administrator';

    $search = $params['search'] ?? '';

    $roleFilter = $params['role'] ?? '';

    $start = (int) ($params['start'] ?? 0);

    $length = (int) ($params['length'] ?? 10);

    $baseQuery = User::query()->with(['roles', 'creator', 'editor', 'deleter']);

    if ($isAdmin) {
      $baseQuery->withTrashed();
    }

    $totalData = (clone $baseQuery)->count();

    if (!empty($search)) {
      $baseQuery->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('username', 'LIKE', "%{$search}%");
      });
    }

    if (!empty($roleFilter)) {
      $baseQuery->whereHas('roles', function ($q) use ($roleFilter) {
        $q->where('name', $roleFilter);
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
        'email' => $row->email,
        'username' => $row->username,
        'role' => $row->roles->pluck('name')->first(),
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
      'roles' => Role::query()->select('name')->distinct()->orderBy('name')->pluck('name'),
    ];
  }
}
