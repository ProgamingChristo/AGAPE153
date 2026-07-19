<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', [
            'users' => User::query()->with('roles')->whereIn('role', ['admin', 'staff'])->orderBy('name')->get(),
            'roles' => Role::query()->with('permissions')->orderBy('label')->get(),
            'permissions' => Permission::query()->orderBy('label')->get(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'staff',
            'status' => 'active',
        ]);
        $user->roles()->sync([$data['role_id']]);

        return back()->with('status', 'Staff user created.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:active,inactive'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $user->update(['role' => $data['role'], 'status' => $data['status']]);
        $user->roles()->sync($data['role_id'] ? [$data['role_id']] : []);

        return back()->with('status', 'Staff user updated.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['label' => $data['label']]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('status', 'Role permissions updated.');
    }
}
