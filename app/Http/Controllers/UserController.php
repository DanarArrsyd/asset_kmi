<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', User::class);

        $users = $this->filteredQuery($request)->get();

        $filename = 'users-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'Email', 'Role', 'Department', 'Dibuat']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->role->label(),
                    $user->department->name ?? '',
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function filteredQuery(Request $request): Builder
    {
        $query = User::query()->with('department');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->value()) {
            $query->where('role', $role);
        }

        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['name', 'email', 'role', 'created_at'], true) ? $sort : 'name';

        $direction = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'roles' => UserRole::cases(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('status', 'created');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'targetUser' => $user,
            'roles' => UserRole::cases(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('status', 'updated');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->role === UserRole::SuperAdmin && User::where('role', UserRole::SuperAdmin)->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus — minimal harus ada 1 Super Admin.');
        }

        $user->delete();

        return back()->with('status', 'deleted');
    }
}
