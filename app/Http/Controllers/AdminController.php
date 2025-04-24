<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct()
    {
    $this->middleware(function ($request, $next) {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }
        return $next($request);
    });
    }

    public function dashboard()
    {
        $users = User::with('branch')->get(); // Eager load the branch relationship
        return view('admin.dashboard', compact('users'));
    }

    
    public function createUser()
    {
        $branches = Branch::all();
        return view('admin.users.create', compact('branches'));
    }

  
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',  // Allow null for branch_id (for admin users)
        ]);
    
        // If the role is admin, set branch_id to null (since admin doesn't need a branch)
        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }
    
        // Create the user
        User::create($request->all());
    
        return redirect()->route('admin.dashboard')->with('success', 'User created successfully!');
    }
    
    
    public function editUser(User $user)
    {
        $branches = Branch::all();
        return view('admin.users.edit', compact('user', 'branches'));
    }

  
    public function updateUser(Request $request, User $user)
    {
      
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, 
            'phone' => 'required|unique:users,phone,' . $user->id, 
            'password' => 'nullable|string|min:6|confirmed', 
            'password_confirmation' => 'nullable|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id', 
        ]);
    
       
        if ($request->has('password') && $request->password) {
            $user->password = bcrypt($request->password);
        }
    
        
        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }
    
       
        $user->update($request->all());
    
        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }
    

   
}
