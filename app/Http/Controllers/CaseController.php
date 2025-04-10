<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lawsuit;
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
        $cases = Lawsuit::orderBy('id','DESC')->paginate(5);
        return view('cases.index', compact('cases'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('cases.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([

        ]);

        $case = Lawsuit::create([
            'name' => $request->name,
        ]);

        return redirect()->route('cases.index')->with('success', 'Case created successfully');
    }

    public function show($id): View
    {
        $case = Lawsuit::findOrFail($id);

        return view('cases.show', compact('case'));
    }

    public function edit($id): View
    {
        $case = Lawsuit::findOrFail($id);
        return view('cases.edit', compact('case'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $case = Lawsuit::findOrFail($id);
        $case->name = $request->name;
        $case->save();

        return redirect()->route('cases.index')->with('success', 'Case updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Lawsuit::findOrFail($id)->delete();
        return redirect()->route('cases.index')->with('success', 'Case deleted successfully');
    }
}
