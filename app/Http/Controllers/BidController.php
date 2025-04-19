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
        $bids = Bid::orderBy('id','DESC')->paginate(10);
        return view('bids.index', compact('bids'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        if (Auth::user()->hasRole('admin')) {
            $lawyers = Lawyer::with('user')->get(); // Show all lawyers to admin
        } elseif (Auth::user()->hasRole('Lawyer')) {
            $lawyers = Lawyer::with('user')
                ->where('user_id', Auth::id()) // Only self for lawyer
                ->get();
        } else {
            $lawyers = collect(); // Empty collection if not admin/lawyer
        }

        $cases = Lawsuit::all();
        return view('bids.create', compact('cases', 'lawyers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'case_id' => 'required|exists:lawsuits,id',
            'lawyer_id' => 'required|exists:lawyers,id',
            'fee' => 'required|numeric',
            'time_estimated' => 'required|date',
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $bid = Bid::create([
            'case_id' => $request->case_id,
            'lawyer_id' => $request->lawyer_id,
            'fee' => $request->fee,
            'time_estimated' => $request->time_estimated,
            'status' => $request->status
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

        if (Auth::user()->hasRole('admin')) {
            $lawyers = Lawyer::with('user')->get(); // Show all lawyers to admin
        } elseif (Auth::user()->hasRole('Lawyer')) {
            $lawyers = Lawyer::with('user')
                ->where('user_id', Auth::id()) // Only self for lawyer
                ->get();
        } else {
            $lawyers = collect(); // Empty collection if not admin/lawyer
        }

        $cases = Lawsuit::all();

        return view('bids.edit', compact('bid', 'cases', 'lawyers'));
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
}
