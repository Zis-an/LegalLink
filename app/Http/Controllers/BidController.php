<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
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
        $bids = Bid::orderBy('id','DESC')->paginate(5);
        return view('bids.index', compact('bids'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('bids.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([

        ]);

        $bid = Bid::create([
            'name' => $request->name,
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
        return view('bids.edit', compact('bid'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $bid = Bid::findOrFail($id);
        $bid->name = $request->name;
        $bid->save();

        return redirect()->route('bids.index')->with('success', 'Bid updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Bid::findOrFail($id)->delete();
        return redirect()->route('bids.index')->with('success', 'Bid deleted successfully');
    }
}
