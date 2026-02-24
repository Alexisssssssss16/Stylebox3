<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = MeasurementUnit::latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $units = $query->get();
        return view('measurement_units.index', compact('units'));
    }

    public function create()
    {
        // Not used, using modal
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:measurement_units,code',
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        MeasurementUnit::create($request->all());

        return redirect()->route('measurement_units.index')->with('success', 'Unidad de medida registrada exitosamente.');
    }

    public function show(MeasurementUnit $measurementUnit)
    {
        //
    }

    public function edit(MeasurementUnit $measurementUnit)
    {
        // Not used, using modal
    }

    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:measurement_units,code,' . $measurementUnit->id,
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $measurementUnit->update($request->all());

        return redirect()->route('measurement_units.index')->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    public function destroy(MeasurementUnit $measurementUnit)
    {
        if ($measurementUnit->products()->exists()) {
            return redirect()->route('measurement_units.index')->with('error', 'No se puede eliminar porque está asociada a productos.');
        }

        $measurementUnit->delete();

        return redirect()->route('measurement_units.index')->with('success', 'Unidad de medida eliminada exitosamente.');
    }
}
