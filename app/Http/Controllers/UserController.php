<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $data = User::with('roles')->latest()->get();

        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        $roles = Role::all();

        return view('users.create',compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:confirm-password',
                'roles' => 'required',
            ]);

            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));

            // Create User
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->save();

            if (!$user->exists) {
                return redirect()->route('users.index')
                    ->with('error', 'Failed to create user.');
            }

            $roles = json_decode($request->input('roles'), true);
            if (isset($roles['name'])) {
                $user->assignRole($roles['name']);
            }

            if ($user->hasRole('client')) {
                $clientDob = $request->input('client_dob');
                $clientAddress = $request->input('client_address');
                $clientPhotoPath = $request->file('client_photo')?->store('clients', 'public');

                // Manually create and save Client data
                $client = new Client();
                $client->user_id = $user->id;
                $client->dob = $clientDob;
                $client->address = $clientAddress;
                $client->photo = $clientPhotoPath;
                $client->save();  // Save client to the database
            }

            if ($user->hasRole('lawyer')) {
                $this->validate($request, [
                    'lawyer_bar_id' => 'required|string|unique:lawyers,bar_id',
                    'lawyer_practice_area' => 'required|string',
                    'lawyer_practice_court' => 'required|string',
                    'lawyer_chamber_name' => 'nullable|string',
                    'lawyer_chamber_address' => 'nullable|string',
                    'lawyer_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                ]);
                $lawyerBarId = $request->input('lawyer_bar_id');
                $lawyerPracticeArea = $request->input('lawyer_practice_area');
                $lawyerPracticeCourt = $request->input('lawyer_practice_court');
                $lawyerChamberName = $request->input('lawyer_chamber_name');
                $lawyerChamberAddress = $request->input('lawyer_chamber_address');
                $lawyerPhotoPath = $request->file('lawyer_photo')?->store('lawyers', 'public');

                // Manually create and save Lawyer data
                $lawyer = new Lawyer();
                $lawyer->user_id = $user->id; // Correctly reference user_id
                $lawyer->bar_id = $lawyerBarId;
                $lawyer->practice_area = $lawyerPracticeArea;
                $lawyer->practice_court = $lawyerPracticeCourt;
                $lawyer->chamber_name = $lawyerChamberName;
                $lawyer->chamber_address = $lawyerChamberAddress;
                $lawyer->photo = $lawyerPhotoPath;
                $lawyer->save();
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function show($id): View
    {
        $user = User::find($id);

        return view('users.show',compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->pluck('name')->toArray();
        $client = $user->client;
        $lawyer = $user->lawyer;

        return view('users.edit', compact('user', 'roles', 'userRole', 'client', 'lawyer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id, // Exclude current user from the unique email validation
            'password' => 'nullable|same:confirm-password', // Password is optional during update
            'roles' => 'required',
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        $roles = json_decode($request->input('roles'), true);
        if (isset($roles['name'])) {
            $user->syncRoles($roles['name']);
        }

        if ($user->hasRole('client')) {
            $client = Client::where('user_id', $user->id)->first();

            if (!$client) {
                return redirect()->route('users.index')
                    ->with('error', 'Client data not found.');
            }

            $client->dob = $request->input('client_dob');
            $client->address = $request->input('client_address');

            if ($request->hasFile('client_photo')) {
                // Delete the old photo if it exists
                if ($client->photo && Storage::disk('public')->exists($client->photo)) {
                    Storage::disk('public')->delete($client->photo);
                }

                // Store the new photo
                $clientPhotoPath = $request->file('client_photo')->store('clients', 'public');
                $client->photo = $clientPhotoPath;
            }

            $client->save();
        }

        if ($user->hasRole('lawyer')) {
            $lawyer = Lawyer::where('user_id', $user->id)->first();

            if (!$lawyer) {
                return redirect()->route('users.index')
                    ->with('error', 'Lawyer data not found.');
            }

            $lawyer->bar_id = $request->input('lawyer_bar_id');
            $lawyer->practice_area = $request->input('lawyer_practice_area');
            $lawyer->practice_court = $request->input('lawyer_practice_court');
            $lawyer->chamber_name = $request->input('lawyer_chamber_name');
            $lawyer->chamber_address = $request->input('lawyer_chamber_address');

            // Only update photo if new one is uploaded
            if ($request->hasFile('lawyer_photo')) {
                // Delete the old photo if it exists
                if ($lawyer->photo && Storage::disk('public')->exists($lawyer->photo)) {
                    Storage::disk('public')->delete($lawyer->photo);
                }

                // Store the new photo
                $lawyerPhotoPath = $request->file('lawyer_photo')->store('lawyers', 'public');
                $lawyer->photo = $lawyerPhotoPath;
            }

            $lawyer->save();  // Save updated lawyer data
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success','User deleted successfully');
    }
}
