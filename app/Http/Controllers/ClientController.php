<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('client_type', $request->type);
        }

        $clients = $query->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        // Using modal
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'boolean',
            'client_type' => 'required|in:virtual,presencial',
        ]);

        $client = Client::create($request->all());

        // If it's a manual client creation (presumably presencial) and we want them in users table
        if ($request->client_type === 'presencial') {
            // (Optional logic: Create a shadow user to link sales properly if needed)
        }

        return redirect()->route('clients.index')->with('success', 'Cliente registrado exitosamente.');
    }

    public function show(Client $client)
    {
        $saleQuery = \App\Models\Sale::where('client_id', $client->id);

        // If the client has a linked user, also include sales by buyer_id
        if ($client->user_id) {
            $saleQuery->orWhere('buyer_id', $client->user_id);
        }

        $sales = $saleQuery->with('details.product')->latest()->paginate(10);

        return view('clients.show', compact('client', 'sales'));
    }

    public function edit(Client $client)
    {
        // Using modal
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'boolean',
            'client_type' => 'required|in:virtual,presencial',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}
