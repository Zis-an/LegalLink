<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LawyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lawyers.list|lawyers.create|lawyers.update|lawyers.delete', ['only' => ['index','store']]);
        $this->middleware('permission:lawyers.create', ['only' => ['create','store']]);
        $this->middleware('permission:lawyers.update', ['only' => ['edit','update']]);
        $this->middleware('permission:lawyers.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $lawyers = Lawyer::orderBy('id','DESC')->paginate(5);
        return view('lawyers.index', compact('lawyers'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('lawyers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([

        ]);

        $lawyer = Lawyer::create([
            'name' => $request->name,
        ]);

        return redirect()->route('lawyers.index')->with('success', 'Lawyer created successfully');
    }

    public function show($id): View
    {
        $lawyer = Lawyer::findOrFail($id);

        return view('lawyers.show', compact('lawyer'));
    }

    public function edit($id): View
    {
        $lawyer = Lawyer::findOrFail($id);
        return view('lawyers.edit', compact('lawyer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $lawyer = Lawyer::findOrFail($id);
        $lawyer->name = $request->name;
        $lawyer->save();

        return redirect()->route('lawyers.index')->with('success', 'Lawyer updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Lawyer::findOrFail($id)->delete();
        return redirect()->route('lawyers.index')->with('success', 'Lawyer deleted successfully');
    }
}
