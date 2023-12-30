<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('hak.access:inventory')->only(['index', 'edit']);
        $this->middleware('hak.access:inventory-manage')->only(['create', 'update', 'destroy']);
    }
    
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Inventory::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('inventory.index');
    }

    public function create(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'code' => ['required', 'string', 'max:255', 'unique:inventories'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'addItem')->withInput();
        }

        try {
            DB::beginTransaction();
            Inventory::create($req->all());
            DB::commit();
            Alert::success('Success', 'Item added successfully.');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Error', 'Failed to add item.');
            return redirect()->back()->with('error', 'Failed to add item.');
        }
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $inventory
        ]);
    }

    public function update(Request $req, $id)
    {
        $validator = Validator::make($req->all(), [
            'code' => ['required', 'string', 'max:255', 'unique:inventories,code,' . $id],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'editItem')->withInput();
        }

        try {
            DB::beginTransaction();
            $inventory = Inventory::findOrFail($id);
            $inventory->update($req->all());
            DB::commit();
            Alert::success('Success', 'Item updated successfully.');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Error', 'Failed to update item.'.$th->getMessage());
            return redirect()->back()->with('error', 'Failed to update item.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $inventory = Inventory::findOrFail($id);
            $inventory->delete();
            DB::commit();
            Alert::success('Success', 'Item deleted successfully.');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::error('Error', 'Failed to delete item.');
            return redirect()->back()->with('error', 'Failed to delete item.');
        }
    }

    public function search(Request $req)
    {
        $minStock = 0;
        if ($req->has('minStock')) {
            $minStock = $req->minStock;
        }
        $data = $req->validate([
            'search' => 'nullable|string',
        ]);
        $search = $req->search;
        if (!$search) {
            return response()->json([
                'success' => false,
                'data' => [],
            ]);
        }

        $inventory = Inventory::where('code', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->where('stock', '>=', $minStock)
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inventory,
        ]);
    }
}
