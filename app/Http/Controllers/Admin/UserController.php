<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleFilter = $request->get('role');
        
        $users = User::with('role')
            ->when($search, function($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($roleFilter, function($query, $roleFilter) {
                $query->where('role_id', $roleFilter);
            })
            ->latest()
            ->get();

        $roles = Role::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'search', 'roles', 'roleFilter'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'full_name' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
        }
        unset($validated['password']);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
}
