<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin');
        });

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', '%' . $buscar . '%')
                ->orWhere('email', 'like', '%' . $buscar . '%');
            });
        }

        if ($request->filled('ver_inactivos')) {
            $query->where('activo', false);
        } else {
            $query->where('activo', true);
        }

        if ($request->filled('rol')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->rol);
            });
        }

        $users = $query->orderBy('name')->paginate(20);
        $roles = Role::all();

        return view('usuarios.index', compact('users', 'roles'));
    }



    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('usuarios.edit', compact('user', 'roles'));
    }
    

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'activo' => 'nullable|in:0,1',
        ]);

        $activo = $request->input('activo', 0);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'activo' => $activo,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }
}
