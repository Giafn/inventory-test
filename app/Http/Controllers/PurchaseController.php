<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('hak.access:purchase')->only(['index', 'show']);
        $this->middleware('hak.access:purchase-manage')->only(['upsert', 'destroy']);
    }
    
    public function index(Request $req)
    {
        $user = Auth::user();
        $userId = $user->id;
        if ($user->role == 1 || $user->role == 4) {
            $userId = null;
        }  

        if ($req->ajax()) {
            $data = Purchase::with('details', 'details.inventory', 'user')
                ->when($userId, function ($query, $userId) {
                    return $query->where('user_id', $userId);
                })
                ->orderBy('purchases.created_at', 'desc')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('purchase.index');
    }

    public function upsert(Request $req)
    {
        $validator =Validator::make($req->all(), [
            'id' => ['nullable', 'numeric', 'exists:purchases,id'],
            'item' => ['required', 'array'],
            'price' => ['required', 'array'],
            'qty' => ['required', 'array'],
            'item.*' => ['required', 'numeric', 'exists:inventories,id'],
            'price.*' => ['required', 'numeric', 'min:10'],
            'qty.*' => ['required', 'numeric', 'min:1'],
        ]);

        $id = $req->id ? $req->id : null;
        if ($validator->fails()) {
            $itemName = Inventory::whereIn('id', $req->item)->orderByRaw('FIELD(id, ' . implode(',', $req->item) . ')')->pluck('name')->toArray();
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back()->withErrors($validator, $id ? 'editItem' : 'addItem')->withInput($req->all())->with('itemName', $itemName);
        }

        $data = [
            'item' => $req->item,
            'price' => $req->price,
            'qty' => $req->qty,
        ];
        
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $lastPurchase = Purchase::orderBy('created_at', 'desc')->where('user_id', $user->id)->first();

            $purchase = Purchase::updateOrCreate(
                ['id' => $id],
                [
                    'user_id' => $user->id,
                    'number' => $user->id . date('Ymd') . ($lastPurchase ? $lastPurchase->id + 1 : 1),
                    'date' => now(),
                ]
            );

            $purchase = Purchase::with('details', 'details.inventory')->findOrFail($purchase->id);
            

            $result = array_map(function ($item, $price, $qty) use ($purchase) {
                return [
                    "purchase_id" => $purchase->id,
                    "inventory_id" => (int)$item,
                    "qty" => $qty,
                    "price" => $price * $qty,
                ];
            }, $data['item'], $data['price'], $data['qty']);

            if ($id) {
                $oldItem = $purchase->details->pluck('inventory_id')->toArray();
                $inventoryData = Inventory::whereIn('id', $oldItem)->get()->toArray();
                $purchaseDetails = $purchase->details;
                foreach ($purchaseDetails as $key => $value) {
                    unset($inventoryData[$key]['created_at']);
                    unset($inventoryData[$key]['updated_at']);
                    $inventoryData[$key]['stock'] -= $value['qty'];
                }
                foreach ($inventoryData as $key => $value) {
                    $inventoryUpdate = Inventory::findOrFail($value['id']);
                    $inventoryUpdate->update($value);
                }
                $purchase->details()->delete();
                DB::commit();
            }

            $purchase->details()->createMany($result);
            
            $inventory = Inventory::whereIn('id', $data['item'])->get()->toArray();

            foreach ($result as $key => $value) {
                unset($inventory[$key]['created_at']);
                unset($inventory[$key]['updated_at']);
                $inventory[$key]['stock'] += $value['qty'];
            }

            if ($id) {
                foreach ($inventory as $key => $value) {
                    $inventoryUpdate = Inventory::findOrFail($value['id']);
                    $inventoryUpdate->update($value);
                }
            } else {
                Inventory::upsert($inventory, ['id'], ['stock']);
            }
            
            Alert::success('Success', $id ? 'Item updated successfully.' : 'Item added successfully.');
            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            Alert::error('Error', 'Failed to add item.');
            return redirect()->back()->with('error', 'Failed to add item.');
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with('details', 'details.inventory')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $purchase
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $purchase = Purchase::findOrFail($id);
            $inventory = Inventory::whereIn('id', $purchase->details->pluck('inventory_id')->toArray())->get()->toArray();
            foreach ($inventory as $key => $value) {
                unset($inventory[$key]['created_at']);
                unset($inventory[$key]['updated_at']);
                $inventory[$key]['stock'] -= $purchase->details[$key]['qty'];
            }
            foreach ($inventory as $key => $value) {
                $inventoryUpdate = Inventory::findOrFail($value['id']);
                $inventoryUpdate->update($value);
            }
            $purchase->details()->delete();
            $purchase->delete();
            DB::commit();
            Alert::success('Success', 'Item deleted successfully.');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Error', 'Failed to delete item.');
            return redirect()->back()->with('error', 'Failed to delete item.');
        }
    }
}
