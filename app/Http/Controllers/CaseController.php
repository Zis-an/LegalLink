<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\Client;
use App\Models\Lawyer;
use App\Models\User;

use App\Notifications\CaseCreatedNotification;
use Illuminate\Http\Request;
use App\Models\Lawsuit;
use Illuminate\Support\Facades\Notification;
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
        $user = auth()->user();

        if ($user->hasRole('client')) {
            $client = Client::where('user_id', $user->id)->first();

            $cases = Lawsuit::with(['client.user', 'bids'])
                ->withCount('bids')
                ->where('client_id', $client->id)
                ->latest()
                ->paginate(10);
        } elseif ($user->hasRole('lawyer')) {
            $lawyer = Lawyer::where('user_id', $user->id)->first();

            $cases = Lawsuit::with(['client.user', 'bids'])
                ->withCount('bids')
                ->where('category', $lawyer->practice_area)
                ->latest()
                ->paginate(10);
        } else {
            $cases = Lawsuit::with(['client.user', 'bids'])
                ->withCount('bids')
                ->latest()
                ->paginate(10);
        }

        return view('cases.index', compact('cases'))->with('i', ($request->input('page', 1) - 1) * 5);
    }


    public function create()
    {
        $user = auth()->user();
        $client = null;

        if ($user->hasRole('lawyer')) {
            return redirect()->route('cases.index')->with('error', 'You are not eligible to create an issue.');
        }

        if ($user->hasRole('client')) {
            $client = Client::with('user')->where('user_id', $user->id)->first();
            $clients = collect([$client]);
        } elseif ($user->hasRole('admin')) {
            $clients = Client::with('user')->get();
        } else {
            $clients = collect();
        }

        return view('cases.create', compact('clients', 'client'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'nullable',
            'description' => 'required|string',
            'voice_note' => 'nullable|file|mimes:mp3,wav',
            'voice_note_blob' => 'nullable|string',
            'category' => 'required|in:civil,criminal',
            'uploaded_file' => 'nullable|file|mimes:jpg,jpeg,png,mp3,mp4|max:102400', // 100MB max
            'country' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
        ]);

        $path = null;

        // Handle traditional file upload
        if ($request->hasFile('voice_note')) {
            $path = $request->file('voice_note')->store('cases', 'public');
        }

        // Handle base64 blob recording (webm)
        if ($request->filled('voice_note_blob')) {
            $base64Audio = $request->voice_note_blob;
            $base64Str = preg_replace('/^data:audio\/webm;base64,/', '', $base64Audio);
            $decoded = base64_decode($base64Str);

            $filename = 'cases/' . uniqid() . '.webm';
            Storage::disk('public')->put($filename, $decoded);

            $path = $filename;
        }

        $uploadedFilePath = null;

        if ($request->hasFile('uploaded_file')) {
            $uploadedFile = $request->file('uploaded_file');
            $uploadedFileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFilePath = $uploadedFile->storeAs('lawsuits/files', $uploadedFileName, 'public');
        }

        $lawsuit = Lawsuit::create([
            'client_id' => $request->client_id,
            'title' => $request->title,
            'description' => $request->description,
            'voice_note' => $path,
            'category' => $request->category,
            'uploaded_file' => $uploadedFilePath,
            'country' => $request->country,
            'division' => $request->division,
            'district' => $request->district,
            'thana' => $request->thana,
        ]);

        event(new PushNotification([
            'author' => $lawsuit->client->user->name,
            'category' => $lawsuit->category,
        ]));

        // Target only lawyers and admins
        $users = User::role(['admin', 'lawyer'])->get();
        Notification::send($users, new CaseCreatedNotification([
            'author' => $lawsuit->client->user->name,
            'category' => $lawsuit->category,
        ]));

        return redirect()->route('cases.index')->with('success', 'Issue created successfully');
    }

    public function show(Lawsuit $case)
    {
        $case->load(['client.user', 'bids.lawyer.user']);
        return view('cases.show', compact('case'));
    }

    public function edit(Lawsuit $case)
    {
        $user = auth()->user();
        $client = null;

        if ($user->hasRole('lawyer')) {
            return redirect()->route('cases.index')->with('error', 'You are not eligible to create an issue.');
        }

        if ($user->hasRole('client')) {
            $client = Client::with('user')->where('user_id', $user->id)->first();
            $clients = collect([$client]);
        } elseif ($user->hasRole('admin')) {
            $clients = Client::with('user')->get();
        } else {
            $clients = collect();
        }

        return view('cases.edit', compact('case', 'clients', 'client'));
    }

    public function update(Request $request, Lawsuit $case)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'description' => 'required|string',
            'voice_note' => 'nullable|file|mimes:mp3,wav',
            'status' => 'required|in:open,in_progress,closed',
            'category' => 'required|in:civil,criminal',
            'uploaded_file' => 'nullable|file|mimes:jpg,jpeg,png,mp3,mp4|max:102400', // 100MB max
            'country' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('voice_note')) {
            if ($case->voice_note && Storage::disk('public')->exists($case->voice_note)) {
                Storage::disk('public')->delete($case->voice_note);
            }

            $case->voice_note = $request->file('voice_note')->store('cases', 'public');
        }

        if ($request->hasFile('uploaded_file')) {
            if ($case->uploaded_file && Storage::disk('public')->exists($case->uploaded_file)) {
                Storage::disk('public')->delete($case->uploaded_file);
            }

            $uploadedFile = $request->file('uploaded_file');
            $uploadedFileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $case->uploaded_file = $uploadedFile->storeAs('lawsuits/files', $uploadedFileName, 'public');
        }

        $case->update([
            'client_id' => $request->client_id,
            'description' => $request->description,
            'status' => $request->status,
            'category' => $request->category,
            'country' => $request->country,
            'division' => $request->division,
            'district' => $request->district,
            'thana' => $request->thana,
        ]);

        $case->save();

        return redirect()->route('cases.index')->with('success', 'Issue updated successfully.');
    }

    public function destroy(Lawsuit $case)
    {
        $user = auth()->user();

        if ($user->hasRole('lawyer')) {
            return redirect()->route('cases.index')->with('error', 'You are not eligible to delete an issue.');
        }

        // Delete voice_note file if exists
        if ($case->voice_note && Storage::disk('public')->exists($case->voice_note)) {
            Storage::disk('public')->delete($case->voice_note);
        }

        // Delete uploaded_file if exists
        if ($case->uploaded_file && Storage::disk('public')->exists($case->uploaded_file)) {
            Storage::disk('public')->delete($case->uploaded_file);
        }

        // Delete the lawsuit record
        $case->delete();

        return redirect()->route('cases.index')->with('success', 'Case deleted successfully.');
    }
}
