<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use DB;
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
        // Step 1: Validate the incoming request
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        // Step 2: Break down and save data manually for User
        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        // Create User
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->save();  // Save user to the database

        // Ensure the user is saved and has a valid ID before proceeding
        if (!$user->exists) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to create user.');
        }

        // Step 3: Manually assign roles (since roles are passed as a JSON string)
        $roles = json_decode($request->input('roles'), true);
        if (isset($roles['name'])) {
            $user->assignRole($roles['name']);
        }

        // Step 4: If the user is a Client, save additional client details
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

        // Step 5: If the user is a Lawyer, save additional lawyer details (if available)
        if ($user->hasRole('lawyer')) {
            $lawyerBarId = $request->input('lawyer_bar_id');
            $lawyerPracticeArea = $request->input('lawyer_practice_area');
            $lawyerChamberName = $request->input('lawyer_chamber_name');
            $lawyerChamberAddress = $request->input('lawyer_chamber_address');
            $lawyerPhotoPath = $request->file('lawyer_photo')?->store('lawyers', 'public');

            // Manually create and save Lawyer data
            $lawyer = new Lawyer();
            $lawyer->user_id = $user->id; // Correctly reference user_id
            $lawyer->bar_id = $lawyerBarId;
            $lawyer->practice_area = $lawyerPracticeArea;
            $lawyer->chamber_name = $lawyerChamberName;
            $lawyer->chamber_address = $lawyerChamberAddress;
            $lawyer->photo = $lawyerPhotoPath;
            $lawyer->save();  // Save lawyer to the database
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function show($id): View
    {
        $user = User::find($id);

        return view('users.show',compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();  // Ensure this returns a Collection
        $userRole = $user->roles->pluck('name')->toArray();  // If you want to get the user's roles
        $client = $user->client;  // Assuming user has a 'client' relationship
        $lawyer = $user->lawyer;  // Assuming user has a 'lawyer' relationship

        return view('users.edit', compact('user', 'roles', 'userRole', 'client', 'lawyer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // Step 1: Validate the incoming request
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id, // Exclude current user from the unique email validation
            'password' => 'nullable|same:confirm-password', // Password is optional during update
            'roles' => 'required',
        ]);

        // Step 2: Find the user by ID and check if exists
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')
                ->with('error', 'User not found.');
        }

        // Step 3: Break down and update data manually for User
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Only update password if it's provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();  // Save user to the database

        // Step 4: Manually assign roles (since roles are passed as a JSON string)
        $roles = json_decode($request->input('roles'), true);
        if (isset($roles['name'])) {
            $user->syncRoles($roles['name']); // Using syncRoles to update roles
        }

        // Step 5: If the user is a Client, update additional client details
        if ($user->hasRole('client')) {
            $client = Client::where('user_id', $user->id)->first();

            if (!$client) {
                return redirect()->route('users.index')
                    ->with('error', 'Client data not found.');
            }

            // Update client details
            $client->dob = $request->input('client_dob');
            $client->address = $request->input('client_address');

            // Only update photo if new one is uploaded
            if ($request->hasFile('client_photo')) {
                // Delete the old photo if it exists
                if ($client->photo && Storage::disk('public')->exists($client->photo)) {
                    Storage::disk('public')->delete($client->photo);
                }

                // Store the new photo
                $clientPhotoPath = $request->file('client_photo')->store('clients', 'public');
                $client->photo = $clientPhotoPath;
            }

            $client->save();  // Save updated client data
        }

        // Step 6: If the user is a Lawyer, update additional lawyer details
        if ($user->hasRole('lawyer')) {
            $lawyer = Lawyer::where('user_id', $user->id)->first();

            if (!$lawyer) {
                return redirect()->route('users.index')
                    ->with('error', 'Lawyer data not found.');
            }

            // Update lawyer details
            $lawyer->bar_id = $request->input('lawyer_bar_id');
            $lawyer->practice_area = $request->input('lawyer_practice_area');
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
