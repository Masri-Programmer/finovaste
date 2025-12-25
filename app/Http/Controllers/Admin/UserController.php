<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserRequest;
use App\Traits\HasAppMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::with('roles')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/Create', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }


    /**
     * Display the specified resource.
     * Note: You might not need a dedicated 'show' page in an admin panel
     * if the 'edit' page contains all info.
     */
    public function show(User $user): Response
    {
        $user->load(['roles', 'listings', 'reviews', 'addresses']);

        return Inertia::render('admin/users/Show', [
            'user' => $user,
        ]);
    }
    public function store(UserRequest $request): RedirectResponse
    {
        // Die Validierung wird jetzt von UserRequest übernommen
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return $this->checkSuccess($user, 'created', 'users.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->syncRoles($validated['roles'] ?? []);

        return $this->checkSuccess($user, 'updated', 'users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return $this->checkError('messages.errors.delete_own_account');
        }

        $user->delete();

        return $this->checkSuccess($user, 'deleted', 'users.index');
    }
}

