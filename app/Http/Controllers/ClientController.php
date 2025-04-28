<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

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
        if( in_array('client', auth()->user()->roles->pluck('name')->toArray() ) ) {
            $clients = Client::with('user')->where('user_id', auth()->id())->get();
        } else {
            $clients = Client::with('user')->orderByDesc('id')->paginate(5);
        }

        return view('clients.index', compact('clients'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        $users = User::role('client')->get();
        return view('clients.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'required',
            'dob' => 'required|date',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')->store('clients', 'public');

        Client::create([
            'user_id' => $request->user_id,
            'address' => $request->address,
            'dob' => $request->dob,
            'photo' => $path,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }

    public function show($id): View
    {
        $client = Client::with('user')->findOrFail($id);
        return view('clients.show', compact('client'));
    }

    public function edit($id): View
    {
        $client = Client::findOrFail($id);
        $users = User::all();
        return view('clients.edit', compact('client', 'users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'required',
            'dob' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('user_id', 'address', 'dob');

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($client->photo && Storage::disk('public')->exists($client->photo)) {
                Storage::disk('public')->delete($client->photo);
            }

            $data['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($client->user_id);

        // Delete photo
        if ($client->photo && Storage::disk('public')->exists($client->photo)) {
            Storage::disk('public')->delete($client->photo);
        }

        $client->delete();
        $user->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }

    public function searchByEmail(Request $request)
    {
        $search = $request->input('q');

        if (!$search || !filter_var($search, FILTER_VALIDATE_EMAIL)) {
            return response()->json([]); // only search if it's a valid email
        }

        $client = Client::with('user')
            ->whereHas('user', function ($query) use ($search) {
                $query->whereRaw('LOWER(email) = ?', [strtolower($search)]);
            })
            ->first();

        if (!$client) {
            return response()->json([]);
        }

        return response()->json([
            [
                'id' => $client->id,
                'text' => $client->user->name . ' (' . $client->user->email . ')'
            ]
        ]);
    }
}
