<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = UserRole::all();
        return view('admin.users_role', compact('users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'username' => 'required|string|max:50|unique:users_role',
        'email' => 'required|email|unique:users_role',
        'password' => 'required|min:8',
        'role' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $image = $request->file('image');

        // Ensure folders exist
        $imagesPath = public_path('images');
        $usersPath = $imagesPath . '/users';
        if (!file_exists($imagesPath)) mkdir($imagesPath, 0755, true);
        if (!file_exists($usersPath)) mkdir($usersPath, 0755, true);

        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move($usersPath, $imageName);
        $imagePath = 'images/users/' . $imageName;
    }

    UserRole::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'image' => $imagePath
    ]);

    return redirect()->back()->with('success', 'User created successfully!');
}


public function update(Request $request, $id)
{
    $user = UserRole::findOrFail($id);

    $request->validate([
        'username' => 'required|string|max:50|unique:users_role,username,' . $id,
        'email' => 'required|email|unique:users_role,email,' . $id,
        'password' => 'nullable|min:8',
        'role' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($user->image && file_exists(public_path($user->image))) {
            unlink(public_path($user->image));
        }

        $image = $request->file('image');

        // Ensure folders exist
        $imagesPath = public_path('images');
        $usersPath = $imagesPath . '/users';

        if (!file_exists($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        if (!file_exists($usersPath)) {
            mkdir($usersPath, 0755, true);
        }

        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move($usersPath, $imageName);
        $user->image = 'images/users/' . $imageName;
    }

    $user->username = $request->username;
    $user->email = $request->email;
    if ($request->password) {
        $user->password = Hash::make($request->password);
    }
    $user->role = $request->role;
    $user->save();

    return redirect()->back()->with('success', 'User updated successfully!');
}


    public function destroy($id)
    {
        $user = UserRole::findOrFail($id);

        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
