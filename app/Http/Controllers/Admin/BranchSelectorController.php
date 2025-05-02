<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchSelectorController extends Controller
{
    public function __construct()
    {
        // Only Admin can access branch selection
        $this->middleware('checkRole:admin');
    }

    public function show()
    {
        $branches = Branch::all();
        return view('admin.select_branch', compact('branches'));
    }

    public function switchBranch()
    {
        $branches = Branch::all();
        return view('admin.switch_branch', compact('branches'));
    }
    
    public function set(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($request->branch_id);

        session([
            'selected_branch_id' => $branch->id,
            'selected_branch_name' => $branch->name,
        ]);

        return redirect()->route('admin.dashboard');
    }
    

}
