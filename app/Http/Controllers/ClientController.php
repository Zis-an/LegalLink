<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:clients.list|clients.create|clients.update|clients.delete', ['only' => ['index','store']]);
        $this->middleware('permission:clients.create', ['only' => ['create','store']]);
        $this->middleware('permission:clients.update', ['only' => ['edit','update']]);
        $this->middleware('permission:clients.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $clients = Client::orderBy('id','DESC')->paginate(5);
        return view('clients.index', compact('clients'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([

        ]);

        $client = Client::create([
            'name' => $request->name,
        ]);

        return redirect()->route('clients.index')->with('success', 'client created successfully');
    }

    public function show($id): View
    {
        $client = Client::findOrFail($id);

        return view('clients.show', compact('client'));
    }

    public function edit($id): View
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $client = Client::findOrFail($id);
        $client->name = $request->name;
        $client->save();

        return redirect()->route('clients.index')->with('success', 'client updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Client::findOrFail($id)->delete();
        return redirect()->route('clients.index')->with('success', 'client deleted successfully');
    }
}
