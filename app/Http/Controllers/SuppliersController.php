<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;

class SuppliersController extends Controller
{
    public function index()
    {
        $suppliers = Suppliers::latest()->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        Suppliers::create($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado');
    }

    public function show(Suppliers $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Suppliers $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Suppliers $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado');
    }

    public function destroy(Suppliers $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado');
    }
}
