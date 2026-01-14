<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role_type', 'user')
                        ->orWhere('role_type', 'admin')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('admin.employees', compact('employees'));
    }


    public function edit($id)
    {
        $employee = User::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role_type' => 'required|in:user,admin',
            'gender' => 'required|in:male,female',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_type' => $request->role_type,
            'gender' => $request->gender,
            'active' => $request->has('active'),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move('users', $imageName); // relative folder
            $data['image'] = $imageName; // store filename in DB
        }

        User::create($data);

        return redirect()->route('employees')->with('success', 'Employee added successfully!');
    }

    public function show($id)
    {
        $employee = User::findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_type' => 'required|in:user,admin',
            'gender' => 'required|in:male,female',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_type' => $request->role_type,
            'gender' => $request->gender,
            'active' => $request->has('active'),
        ];

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image == 1) {
            if ($employee->image && File::exists(public_path('users/' . $employee->image))) {
                File::delete(public_path('users/' . $employee->image));
            }
            $data['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($employee->image && File::exists(public_path('users/' . $employee->image))) {
                File::delete(public_path('users/' . $employee->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move('users', $imageName); // relative folder
            $data['image'] = $imageName; // store filename in DB
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('employees')->with('success', 'Employee updated successfully!');
    }

    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        
        // Delete image if exists
        if ($employee->image && File::exists(public_path('users/' . $employee->image))) {
            File::delete(public_path('users/' . $employee->image));
        }

        $employee->delete();

        return redirect()->route('employees')->with('success', 'Employee deleted successfully!');
    }
}