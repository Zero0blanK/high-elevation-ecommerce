<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $Admins = Admin::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.admin-users.index', compact('Admins'));
    }

    public function create()
    {
        $roles = ['super_admin', 'admin', 'manager', 'staff'];
        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,manager,staff',
            'is_active' => 'boolean',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Admin user created successfully.');
    }

    public function show(Admin $Admin)
    {
        return view('admin.admin-users.show', compact('Admin'));
    }

    public function edit(Admin $Admin)
    {
        $roles = ['super_admin', 'admin', 'manager', 'staff'];
        return view('admin.admin-users.edit', compact('Admin', 'roles'));
    }

    public function update(Request $request, Admin $Admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admin_users')->ignore($Admin->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,manager,staff',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $Admin->update($data);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    public function destroy(Admin $Admin)
    {
        // Prevent deleting the last super admin
        if ($Admin->role === 'super_admin') {
            $superAdminCount = Admin::where('role', 'super_admin')->where('is_active', true)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Cannot delete the last active super admin.');
            }
        }

        // Prevent self-deletion
        if ($Admin->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $Admin->delete();

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}