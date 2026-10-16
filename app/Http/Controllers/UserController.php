<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:admin,lecturer'],
            'is_active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,name,username,email,role,is_active,created_at'],
            'direction' => ['nullable', 'in:asc,desc'],
            'perPage' => ['nullable', 'in:10,20,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = User::query();
        if (! empty($filters['search'])) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$filters['search'].'%')
                ->orWhere('username', 'like', '%'.$filters['search'].'%')
                ->orWhere('email', 'like', '%'.$filters['search'].'%'));
        }
        foreach (['role', 'is_active'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        $users = $query->orderBy($filters['sort'] ?? 'id', $filters['direction'] ?? 'desc')
            ->paginate($filters['perPage'] ?? 10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = new User;
        $user->fill(collect($data)->except(['role', 'is_active'])->all());
        $user->role = $data['role'];
        $user->is_active = $data['is_active'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'Đã tạo tài khoản.');
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($user, $data) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($user->role === 'admin'
                && ($data['role'] !== 'admin' || ! $data['is_active'])
                && ($admins->count() <= 1 || ($user->is_active && $admins->where('is_active', true)->count() <= 1))) {
                throw ValidationException::withMessages(['role' => 'Không thể khóa hoặc hạ quyền Admin hoạt động cuối cùng.']);
            }
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user->fill(collect($data)->except(['role', 'is_active'])->all());
            $user->role = $data['role'];
            $user->is_active = $data['is_active'];
            $user->save();
        });

        return redirect()->route('users.index')->with('success', 'Đã cập nhật tài khoản.');
    }

    public function destroy(User $user): RedirectResponse
    {
        DB::transaction(function () use ($user) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($user->role === 'admin' && ($admins->count() <= 1
                || ($user->is_active && $admins->where('is_active', true)->count() <= 1))) {
                throw ValidationException::withMessages(['user' => 'Không thể xóa Admin hoạt động cuối cùng.']);
            }
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'Đã xóa tài khoản.');
    }
}
