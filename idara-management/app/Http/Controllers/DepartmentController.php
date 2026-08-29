<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Orodha ya idara. Hakuna filtering ya ziada hapa kwa makusudi - Global
     * Scope (DepartmentVisibilityScope) tayari inahakikisha Admin anaona zote
     * na Kiongozi/Mwanachama wanaona zao tu. Angalia app/Models/Department.php.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Department::class);

        $departments = Department::withCount(['leaders', 'members'])
            ->orderBy('name')
            ->paginate(15);

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        $this->authorize('create', Department::class);

        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "Idara '{$department->name}' imeundwa.");
    }

    public function show(Department $department): View
    {
        $this->authorize('view', $department);

        $department->load(['leaders', 'members']);

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $this->authorize('update', $department);

        return view('departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "Idara '{$department->name}' imesasishwa.");
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('delete', $department);

        $name = $department->name;
        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('status', "Idara '{$name}' imefutwa.");
    }
}
