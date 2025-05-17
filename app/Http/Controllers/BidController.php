<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Lawsuit;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BidController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bids.list|bids.create|bids.update|bids.delete', ['only' => ['index','store']]);
        $this->middleware('permission:bids.create', ['only' => ['create','store']]);
        $this->middleware('permission:bids.update', ['only' => ['edit','update']]);
        $this->middleware('permission:bids.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $bids = Bid::with('lawyer.user', 'case')->orderByDesc('id')->paginate(10);

        } elseif ($user->hasRole('lawyer')) {
            $lawyer = $user->lawyer;
            $bids = Bid::with('lawyer.user', 'case')
                ->where('lawyer_id', $lawyer->id)
                ->orderByDesc('id')
                ->paginate(10);

        } elseif ($user->hasRole('client')) {
            $client = $user->client;

            // Get all lawsuit IDs belonging to this client
            $caseIds = \App\Models\Lawsuit::where('client_id', $client->id)->pluck('id');

            $bids = Bid::with('lawyer.user', 'case')
                ->whereIn('case_id', $caseIds)
                ->orderByDesc('id')
                ->paginate(10);
        } else {
            // No access if role is not recognized
            abort(403);
        }

        return view('bids.index', compact('bids'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create($caseId): View
    {
        $case = Lawsuit::findOrFail($caseId);

        if (!Auth::user()->hasRole(['lawyer', 'admin'])) {
            abort(403, 'Only lawyers and admins can bid.');
        }

        $lawyers = Lawyer::where('user_id', Auth::id())->first();
        $lawyer = null;

        if (auth()->user()->hasRole('lawyer')) {
            $lawyer = auth()->user()->lawyer;
        }

        if (!$lawyers) {
            $lawyers = Lawyer::all(); // Admin case: list all lawyers
        } else {
            $lawyers = collect([$lawyers]); // Lawyer case: single lawyer inside a collection
        }

        return view('bids.create', compact('case', 'lawyers', 'lawyer'));
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'case_id' => 'required|exists:lawsuits,id',
            'lawyer_id' => 'required|exists:lawyers,id',
            'fee' => 'required|numeric',
            'time_estimated' => 'required|date',
        ]);

        $exists = Bid::where('case_id', $request->case_id)
            ->where('lawyer_id', $request->lawyer_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already bid on this issue.');
        }

        $case = Lawsuit::findOrFail($request->case_id);

        if ($case->accepted_bid_id) {
            return back()->with('error', 'Bidding is closed for this issue.');
        }

        $bid = Bid::create([
            'case_id' => $request->case_id,
            'lawyer_id' => $request->lawyer_id,
            'fee' => $request->fee,
            'time_estimated' => $request->time_estimated,
        ]);

        return redirect()->route('bids.index')->with('success', 'Bid created successfully');
    }

    public function show($id): View
    {
        $bid = Bid::findOrFail($id);
        return view('bids.show', compact('bid'));
    }

    public function edit($id): View
    {
        $bid = Bid::findOrFail($id);
        $lawyers = collect(); // Always define as a collection

        if (Auth::user()->hasRole('admin')) {
            $lawyers = Lawyer::with('user')->get(); // Collection for admin
        }

        $lawyer = null;
        if (auth()->user()->hasRole('lawyer')) {
            $lawyer = auth()->user()->lawyer; // Single lawyer for logged-in lawyer
        }

        $cases = Lawsuit::all();

        return view('bids.edit', compact('bid', 'cases', 'lawyers', 'lawyer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'case_id' => 'required|exists:lawsuits,id',
            'lawyer_id' => 'required|exists:lawyers,id',
            'fee' => 'required|numeric',
            'time_estimated' => 'required|date',
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $bid = Bid::findOrFail($id);
        $bid->case_id = $request->case_id;
        $bid->lawyer_id = $request->lawyer_id;
        $bid->fee = $request->fee;
        $bid->time_estimated = $request->time_estimated;
        $bid->status = $request->status;
        $bid->save();

        return redirect()->route('bids.index')->with('success', 'Bid updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Bid::findOrFail($id)->delete();
        return redirect()->route('bids.index')->with('success', 'Bid deleted successfully');
    }

    public function accept($id): RedirectResponse
    {
        $bid = Bid::with('case')->findOrFail($id);
        $case = $bid->case;

        if ($case->accepted_bid_id) {
            return back()->with('error', 'A bid has already been accepted for this issue.');
        }

        $bid->status = 'accepted';
        $bid->save();

        // Reject other bids for the same case
        Bid::where('case_id', $case->id)
            ->where('id', '!=', $bid->id)
            ->update(['status' => 'rejected']);

        $case->accepted_bid_id = $bid->id;
        $case->status = 'in_progress';
        $case->save();

        return back()->with('success', 'Bid accepted successfully.');
    }
}
