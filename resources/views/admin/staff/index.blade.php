@extends('layouts.admin')

@section('title', 'Staff & Permissions - Admin Agape153')

@section('content')
    <div>
        <p class="text-sm font-black uppercase tracking-[0.22em] text-teal-700">Staff</p>
        <h1 class="mt-3 text-4xl font-black text-slate-950">Roles and permissions.</h1>
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Create Staff</h2>
            <form class="mt-5 grid gap-3" method="POST" action="{{ route('admin.staff.users.store') }}">
                @csrf
                <input class="rounded-xl border border-slate-200 px-4 py-3" name="name" placeholder="Name" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="email" name="email" placeholder="Email" required>
                <input class="rounded-xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="Password" required>
                <select class="rounded-xl border border-slate-200 px-4 py-3" name="role_id" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->label }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" type="submit">Create Staff</button>
            </form>

            <h2 class="mt-8 text-xl font-black text-slate-950">Admin Users</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($users as $user)
                    <form class="grid gap-3 rounded-xl bg-[#f8faf9] p-4" method="POST" action="{{ route('admin.staff.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="font-black text-slate-950">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <select class="rounded-xl border border-slate-200 px-4 py-3" name="role">
                                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                <option value="staff" @selected($user->role === 'staff')>Staff</option>
                            </select>
                            <select class="rounded-xl border border-slate-200 px-4 py-3" name="status">
                                <option value="active" @selected($user->status === 'active')>Active</option>
                                <option value="inactive" @selected($user->status === 'inactive')>Inactive</option>
                            </select>
                            <select class="rounded-xl border border-slate-200 px-4 py-3" name="role_id">
                                <option value="">No role group</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected($user->roles->contains($role))>{{ $role->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn-secondary justify-self-start" type="submit">Save User</button>
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black text-slate-950">Role Permissions</h2>
            <div class="mt-5 grid gap-4">
                @foreach ($roles as $role)
                    <form class="rounded-xl border border-slate-100 bg-[#f8faf9] p-4" method="POST" action="{{ route('admin.staff.roles.update', $role) }}">
                        @csrf
                        @method('PUT')
                        <input class="w-full rounded-xl border border-slate-200 px-4 py-3 font-black" name="label" value="{{ $role->label }}" required>
                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center gap-2 rounded-lg bg-white p-3 text-sm font-bold">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($role->permissions->contains($permission))>
                                    {{ $permission->label }}
                                </label>
                            @endforeach
                        </div>
                        <button class="btn-secondary mt-4" type="submit">Save Role</button>
                    </form>
                @endforeach
            </div>
        </section>
    </div>
@endsection
