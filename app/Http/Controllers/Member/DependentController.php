<?php

namespace App\Http\Controllers\Member;

use App\Actions\Members\AddDependentAction;
use App\Actions\Members\UpdateDependentAction;
use App\Http\Controllers\Controller;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DependentController extends Controller
{
    /**
     * Display a listing of dependents.
     */
    public function index(): View
    {
        $dependents = Auth::user()->dependents;

        return view('member.dependents.index', [
            'dependents' => $dependents,
        ]);
    }

    /**
     * Show the form for creating a new dependent.
     */
    public function create(): View
    {
        return view('member.dependents.create');
    }

    /**
     * Store a newly created dependent.
     */
    public function store(Request $request, AddDependentAction $addDependent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'relationship' => 'required|string|max:50',
        ]);

        $dependent = $addDependent->execute(Auth::id(), $validated);

        return redirect()->route('dependent.index')
            ->with('status', __('Dependent added successfully'));
    }

    /**
     * Show the form for editing the specified dependent.
     */
    public function edit(Dependent $dependent): View
    {
        // Check if the dependent belongs to the authenticated user
        if ($dependent->user_id !== Auth::id()) {
            abort(403);
        }

        return view('member.dependents.edit', [
            'dependent' => $dependent,
        ]);
    }

    /**
     * Update the specified dependent.
     */
    public function update(Request $request, Dependent $dependent, UpdateDependentAction $updateDependent)
    {
        // Check if the dependent belongs to the authenticated user
        if ($dependent->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'relationship' => 'required|string|max:50',
        ]);

        $updateDependent->execute($dependent->id, $validated);

        return redirect()->route('dependent.index')
            ->with('status', __('Dependent updated successfully'));
    }

    /**
     * Remove the specified dependent.
     */
    public function destroy(Dependent $dependent)
    {
        // Check if the dependent belongs to the authenticated user
        if ($dependent->user_id !== Auth::id()) {
            abort(403);
        }

        $dependent->delete();

        return redirect()->route('dependent.index')
            ->with('status', __('Dependent removed successfully'));
    }
}
