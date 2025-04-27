<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        $query = Investor::query();

        if (session('active_branch_id')) {
            $query->where('branch_id', session('active_branch_id'));
        }

        $investors = $query->latest()->paginate(20);

        return view('admin.investors.index', compact('investors'));
    }

    public function create()
    {
        return view('admin.investors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_investment' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,closed',
        ]);

        Investor::create(array_merge($validated, [
            'branch_id' => session('active_branch_id'),
        ]));

        return redirect()->route('admin.investors.index')->with('success', 'Investor created successfully.');
    }

    public function edit(Investor $investor)
    {
        return view('admin.investors.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_investment' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,closed',
        ]);

        $investor->update($validated);

        return redirect()->route('admin.investors.index')->with('success', 'Investor updated successfully.');
    }

    public function show(Investor $investor)
    {
        return view('admin.investors.show', compact('investor'));
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();

        return redirect()->route('admin.investors.index')->with('success', 'Investor deleted successfully.');
    }
}
