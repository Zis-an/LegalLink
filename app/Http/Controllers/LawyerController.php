<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        if( in_array('lawyer', auth()->user()->roles->pluck('name')->toArray() ) ) {
            $lawyers = Lawyer::with('user')->where('user_id', auth()->id())->get();
        } else {
            $lawyers = Lawyer::with('user')->orderByDesc('id')->paginate(5);
        }

        return view('lawyers.index', compact('lawyers'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        $users = User::role('lawyer')->get();
        return view('lawyers.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'bar_id' => 'required',
            'user_id' => 'required|exists:users,id',
            'practice_area' => 'required',
            'chamber_name' => 'required',
            'chamber_address' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')->store('lawyers', 'public');

        Lawyer::create([
            'bar_id' => $request->bar_id,
            'user_id' => $request->user_id,
            'practice_area' => $request->practice_area,
            'chamber_name' => $request->chamber_name,
            'chamber_address' => $request->chamber_address,
            'photo' => $path,
        ]);

        return redirect()->route('lawyers.index')->with('success', 'Lawyer created successfully');
    }

    public function show($id): View
    {
        $lawyer = Lawyer::with('user')->findOrFail($id);
        return view('lawyers.show', compact('lawyer'));
    }

    public function edit($id): View
    {
        $lawyer = Lawyer::findOrFail($id);
        $users = User::role('lawyer')->get();
        return view('lawyers.edit', compact('lawyer', 'users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $lawyer = Lawyer::findOrFail($id);

        $request->validate([
            'bar_id' => 'required',
            'user_id' => 'required|exists:users,id',
            'practice_area' => 'required',
            'chamber_name' => 'required',
            'chamber_address' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('bar_id', 'user_id', 'practice_area', 'chamber_name', 'chamber_address');

        if ($request->hasFile('photo')) {
            if ($lawyer->photo && Storage::disk('public')->exists($lawyer->photo)) {
                Storage::disk('public')->delete($lawyer->photo);
            }
            $data['photo'] = $request->file('photo')->store('lawyers', 'public');
        }

        $lawyer->update($data);

        return redirect()->route('lawyers.index')->with('success', 'Lawyer updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $lawyer = Lawyer::findOrFail($id);
        $user = User::findOrFail($lawyer->user_id);

        if ($lawyer->photo && Storage::disk('public')->exists($lawyer->photo)) {
            Storage::disk('public')->delete($lawyer->photo);
        }

        $lawyer->delete();
        $user->delete(); // Optional: if you're deleting the associated user

        return redirect()->route('lawyers.index')->with('success', 'Lawyer deleted successfully');
    }
}
