<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }
    
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:5|unique:branches,code', 
        ]);

      
        Branch::create([
            'name' => $request->name,
            // 'code' => $request->code,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:5|unique:branches,code,' . $id, // Ignore uniqueness for the current branch
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'name' => $request->name,
            // 'code' => $request->code,
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }
}
