<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Lawsuit;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cases.list|cases.create|cases.update|cases.delete', ['only' => ['index','store']]);
        $this->middleware('permission:cases.create', ['only' => ['create','store']]);
        $this->middleware('permission:cases.update', ['only' => ['edit','update']]);
        $this->middleware('permission:cases.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $cases = Lawsuit::with('client.user')->latest()->paginate(10);;
        return view('cases.index', compact('cases'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        $clients = Client::with('user')->get();
        return view('cases.create', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voice_note' => 'nullable|file|mimes:mp3,wav',
            'status' => 'required|in:open,in_progress,closed',
        ]);

        $path = null;
        if ($request->hasFile('voice_note')) {
            $path = $request->file('voice_note')->store('cases', 'public');
        }

        Lawsuit::create([
            'client_id' => $request->client_id,
            'title' => $request->title,
            'description' => $request->description,
            'voice_note' => $path,
            'status' => $request->status,
        ]);

        return redirect()->route('cases.index')->with('success', 'Case created successfully');
    }

    public function show(Lawsuit $case)
    {
        $case->load('client.user');
        return view('cases.show', compact('case'));
    }

    public function edit(Lawsuit $case)
    {
        $clients = Client::with('user')->get();
        return view('cases.edit', compact('case', 'clients'));
    }

    public function update(Request $request, Lawsuit $case)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voice_note' => 'nullable|file|mimes:mp3,wav',
            'status' => 'required|in:open,in_progress,closed',
        ]);

        if ($request->hasFile('voice_note')) {
            if ($case->voice_note && Storage::disk('public')->exists($case->voice_note)) {
                Storage::disk('public')->delete($case->voice_note);
            }

            $case->voice_note = $request->file('voice_note')->store('cases', 'public');
        }

        $case->update([
            'client_id' => $request->client_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('cases.index')->with('success', 'Case updated successfully.');
    }

    public function destroy(Lawsuit $case)
    {
        if ($case->voice_note && Storage::disk('public')->exists($case->voice_note)) {
            Storage::disk('public')->delete($case->voice_note);
        }

        $case->delete();

        return redirect()->route('cases.index')->with('success', 'Case deleted successfully.');
    }
}
