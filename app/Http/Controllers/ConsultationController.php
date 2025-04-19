<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Consultation;
use App\Models\Lawsuit;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::user()->hasRole('admin')) {
            $lawyers = Lawyer::with('user')->get();
            $clients = Client::with('user')->get();
            $cases = Lawsuit::all();
        } elseif (Auth::user()->hasRole('Lawyer')) {
            $lawyers = Lawyer::with('user')
                ->where('user_id', Auth::id()) // Only self for lawyer
                ->get();
            $clients = Client::with('user')->get();
            $cases = Lawsuit::all();
        } else {
            $lawyers = collect(); // Empty collection if not admin/lawyer
            $clients = collect(); // Empty collection if not admin/lawyer
            $cases = collect(); // Empty collection if not admin/lawyer
        }
        return view('consultations.create', compact('lawyers', 'clients', 'cases'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'lawyer_id' => 'required|exists:lawyers,id',
            'case_id' => 'required|exists:lawsuits,id',
            'date_and_time' => 'required|date_format:Y-m-d\TH:i', // for datetime-local HTML input
            'mode' => 'required|in:virtual,physical',
        ]);

        $consultation = Consultation::create([
            'client_id' => $request->client_id,
            'lawyer_id' => $request->lawyer_id,
            'case_id' => $request->case_id,
            'date_and_time' => $request->date_and_time,
            'mode' => $request->mode,
        ]);

        return redirect()->route('consultations.index')->with('success', 'Consultation created successfully');
    }

    public function show($id): View
    {
        $consultation = Consultation::with(['client.user', 'lawyer.user', 'case'])->findOrFail($id);
        return view('consultations.show', compact('consultation'));
    }

    public function edit($id): View
    {
        $consultation = Consultation::findOrFail($id);

        if (Auth::user()->hasRole('admin')) {
            $lawyers = Lawyer::with('user')->get();
            $clients = Client::with('user')->get();
            $cases = Lawsuit::all();
        } elseif (Auth::user()->hasRole('Lawyer')) {
            $lawyers = Lawyer::with('user')
                ->where('user_id', Auth::id()) // Only self for lawyer
                ->get();
            $clients = Client::with('user')->get();
            $cases = Lawsuit::all();
        } else {
            $lawyers = collect(); // Empty collection if not admin/lawyer
            $clients = collect(); // Empty collection if not admin/lawyer
            $cases = collect(); // Empty collection if not admin/lawyer
        }

        return view('consultations.edit', compact('consultation', 'lawyers', 'clients', 'cases'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'lawyer_id' => 'required|exists:lawyers,id',
            'case_id' => 'required|exists:lawsuits,id',
            'date_and_time' => 'required|date_format:Y-m-d\TH:i', // match datetime-local input
            'mode' => 'required|in:virtual,physical',
        ]);

        $consultation = Consultation::findOrFail($id);

        $consultation->update([
            'client_id' => $request->client_id,
            'lawyer_id' => $request->lawyer_id,
            'case_id' => $request->case_id,
            'date_and_time' => $request->date_and_time,
            'mode' => $request->mode,
        ]);

        return redirect()->route('consultations.index')->with('success', 'Consultation updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        consultation::findOrFail($id)->delete();
        return redirect()->route('consultations.index')->with('success', 'Consultation deleted successfully');
    }
}
