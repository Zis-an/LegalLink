<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use App\Models\LawyerVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LawyerVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lawyer_verifications.list|lawyer_verifications.create|lawyer_verifications.update|lawyer_verifications.delete', ['only' => ['index','store']]);
        $this->middleware('permission:lawyer_verifications.create', ['only' => ['create','store']]);
        $this->middleware('permission:lawyer_verifications.update', ['only' => ['edit','update']]);
        $this->middleware('permission:lawyer_verifications.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        if (auth()->user()->hasRole('lawyer')) {
            // Only show current lawyer's own verifications
            $lawyer = auth()->user()->lawyer;
            $verifications = LawyerVerification::where('lawyer_id', $lawyer->id)->get();
        } else {
            // Admins see all
            $verifications = LawyerVerification::all();
        }

        return view('lawyer_verifications.index', compact('verifications'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        $lawyers = Lawyer::with('user')->get();
        return view('lawyer_verifications.create', compact('lawyers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('lawyer_verifications', 'public');
        }

        LawyerVerification::create([
            'lawyer_id' => $request->lawyer_id,
            'document_path' => $path,
            'status' => 'Pending',
        ]);

        return redirect()->route('lawyer-verifications.index')->with('success', 'Verification request submitted.');
    }

    public function show($id): View
    {
        $verification = LawyerVerification::with(['lawyer.user', 'reviewer'])->findOrFail($id);
        return view('lawyer_verifications.show', compact('verification'));
    }

    public function edit($id): View
    {
        $verification = LawyerVerification::with('lawyer.user')->findOrFail($id);
        return view('lawyer_verifications.edit', compact('verification'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $verification = LawyerVerification::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        $verification->update([
            'status' => $request->status,
            'comment' => $request->comment,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('lawyer-verifications.index')->with('success', 'Verification updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $verification = LawyerVerification::findOrFail($id);

        if ($verification->document_path) {
            Storage::disk('public')->delete($verification->document_path);
        }

        $verification->delete();

        return redirect()->route('lawyer-verifications.index')->with('success', 'Verification deleted.');
    }
}
