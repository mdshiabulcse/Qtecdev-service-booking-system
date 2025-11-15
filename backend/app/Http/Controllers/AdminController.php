<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $users = User::when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'users' => $users,
            'total' => $users->total()
        ]);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|in:admin,teacher,parent'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => ['sometimes', Rules\Password::defaults()],
            'role' => 'sometimes|in:admin,teacher,parent'
        ]);

        $updateData = [];
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        if ($request->has('phone')) {
            $updateData['phone'] = $request->phone;
        }
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        if ($request->has('role')) {
            $updateData['role'] = $request->role;
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function getSystemStats()
    {
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $teacherUsers = User::where('role', 'teacher')->count();
        $parentUsers = User::where('role', 'parent')->count();

        $recentUsers = User::where('created_at', '>=', now()->subDays(7))
            ->count();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'admin_users' => $adminUsers,
                'teacher_users' => $teacherUsers,
                'parent_users' => $parentUsers,
                'recent_users' => $recentUsers
            ]
        ]);
    }
}
