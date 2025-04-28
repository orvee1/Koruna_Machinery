<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchSelectorController extends Controller
{
    // public function __construct()
    // {
    //     // Only Admin can access branch selection
    //     $this->middleware('checkRole:admin');
    // }

    public function show()
    {
        $branches = Branch::all();
        return view('admin.select_branch', compact('branches'));
    }

    public function set(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        session(['active_branch_id' => $request->branch_id]);

        return redirect()->route('admin.dashboard')->with('success', 'Branch selected successfully.');
    }
}
