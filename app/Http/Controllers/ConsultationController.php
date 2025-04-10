<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:consultations.list|consultations.create|consultations.update|consultations.delete', ['only' => ['index','store']]);
        $this->middleware('permission:consultations.create', ['only' => ['create','store']]);
        $this->middleware('permission:consultations.update', ['only' => ['edit','update']]);
        $this->middleware('permission:consultations.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $consultations = Consultation::orderBy('id','DESC')->paginate(5);
        return view('consultations.index', compact('consultations'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('consultations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([

        ]);

        $consultation = consultation::create([
            'name' => $request->name,
        ]);

        return redirect()->route('consultations.index')->with('success', 'Consultation created successfully');
    }

    public function show($id): View
    {
        $consultation = Consultation::findOrFail($id);

        return view('consultations.show', compact('consultation'));
    }

    public function edit($id): View
    {
        $consultation = Consultation::findOrFail($id);
        return view('consultations.edit', compact('consultation'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $consultation = Consultation::findOrFail($id);
        $consultation->name = $request->name;
        $consultation->save();

        return redirect()->route('consultations.index')->with('success', 'Consultation updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        consultation::findOrFail($id)->delete();
        return redirect()->route('consultations.index')->with('success', 'Consultation deleted successfully');
    }
}
