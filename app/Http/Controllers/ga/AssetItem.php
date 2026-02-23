<?php

namespace App\Http\Controllers\ga;

use Throwable;
use App\Models\Company;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Events\SystemResourceUpdated;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreAssetItemRequest;
use App\Models\AssetItem as ModelsAssetItem;
use App\Http\Requests\UpdateAssetItemRequest;

class AssetItem extends Controller
{
  public function view()
  {
    return view('content.ga.asset_items');
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $isAdmin = $user->username === 'administrator';

    $search = $request->input('search.value');

    $query = ModelsAssetItem::query()->select([
      'asset_items.id',
      'asset_items.asset_type_id',
      'asset_items.item_code',
      'asset_items.item_brand',
      'asset_items.item_model',
      'asset_items.item_specification',
      'asset_items.company_id',
      'asset_items.item_status',
      'asset_items.deleted_at'
    ])->with(['type:id,type_name,asset_category_id', 'type.category:id,category_code', 'company:id,company_code'])->when($isAdmin, fn($q) => $q->withTrashed());

    $totalData = $query->count();

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('item_code', 'LIKE', "%{$search}%");
      });
    }

    $totalFiltered = $query->count();

    $assetItems = $query->offset($request->input('start'))->limit($request->input('length'))->latest()->get();

    $data = [];

    if (!empty($assetItems)) {
      $ids = $request->input('start');
      foreach ($assetItems as $item) {
        $nestedData['fake_id'] = ++$ids;
        $nestedData['id'] = $item->id;
        $nestedData['item_code'] = $item->item_code;
        $nestedData['asset_category'] = $item->type->category->category_code;
        $nestedData['asset_type'] = $item->type->type_name;
        $nestedData['item_brand'] = $item->item_brand;
        $nestedData['item_specification'] = $item->item_model ? "{$item->item_model} {$item->item_specification}" : $item->item_specification;
        $nestedData['placement'] = '';
        $nestedData['item_status'] = $item->item_status;
        $nestedData['deleted_at'] = $item->deleted_at;

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data
    ]);
  }

  public function store(StoreAssetItemRequest $request)
  {
    try {
      $assetItem = DB::transaction(function () use ($request) {

        $assetType = AssetType::with('category')->findOrFail($request->asset_type_id);
        $company   = Company::findOrFail($request->company_id);

        $prefix = 'AST';
        $baseCode = "{$prefix}-{$company->company_code}-{$assetType->category->category_code}-{$assetType->type_code}";

        $lastItem = ModelsAssetItem::where('item_code', 'like', $baseCode . '-%')->lockForUpdate()->orderByDesc('item_code')->first();
        $nextNumber = 1;

        if ($lastItem) {
          $lastNumber = (int) substr($lastItem->item_code, -6);
          $nextNumber = $lastNumber + 1;
        }
        $runningNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        $data = $request->validated();
        $data['item_code'] = "{$baseCode}-{$runningNumber}";

        return ModelsAssetItem::create($data);
      });

      event(new SystemResourceUpdated(
        resource: 'asset_items',
        action: 'store',
        performedBy: auth()->id(),
        message: null,
        notifyAuthor: false
      ));

      return response()->json(['status' => 'success', 'message' => "Asset: {$assetItem->item_code} created successfully"], 201);
    } catch (Throwable $e) {
      Log::error('Unexpected error while processing request', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response()->json(['status' => 'danger', 'message' => 'An error occurred while processing your request', 'errors' => $e], 500);
    }
  }

  public function qr(ModelsAssetItem $assetItem)
  {
    $url = route('assets.public.show', $assetItem);

    return response(QrCode::size(300)->generate($url), 200, ['Content-Type' => 'image/svg+xml']);
  }

  public function publicShow(ModelsAssetItem $assetItem)
  {
    if ($assetItem->deleted_at) {
      abort(404);
    }

    $pageConfigs = ['myLayout' => 'blank'];

    return view('content.ga.asset_items-public', compact('assetItem', 'pageConfigs'));
  }

  public function show(ModelsAssetItem $assetItem)
  {
    $publicUrl = route('assets.public.show', $assetItem->public_code);
    $qrUrl = route('asset_items.qr', $assetItem->id);

    return view('content.ga.asset_items-show', compact('assetItem', 'publicUrl', 'qrUrl'));
  }

  public function edit(ModelsAssetItem $assetItem)
  {
    $assetItem->load(['type:id,type_name', 'company:id,company_name']);

    return response()->json($assetItem, 200);
  }

  public function update(UpdateAssetItemRequest $request, ModelsAssetItem $assetItem)
  {
    //
  }

  public function destroy(ModelsAssetItem $assetItem)
  {
    //
  }

  public function restore(string $id)
  {
    $assetItem = ModelsAssetItem::withTrashed()->findOrFail($id);
  }

  public function force(string $id)
  {
    $assetItem = ModelsAssetItem::withTrashed()->findOrFail($id);
  }
}
