<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Angalia architecture.md §2.6 na §5. Data hii ni nyeti (fedha) - angalia
 * TransactionPolicy: mwanachama wa kawaida hana ufikiaji hata kidogo, ni
 * Kiongozi/Admin pekee.
 */
class DepartmentTransactionController extends Controller
{
    public function index(Department $department): View
    {
        $this->authorize('viewAny', [DepartmentTransaction::class, $department]);

        $transactions = $department->transactions()
            ->with('recorder')
            ->latest('occurred_at')
            ->paginate(20);

        $total = (clone $department->transactions())->sum('amount');

        return view('transactions.index', compact('department', 'transactions', 'total'));
    }

    public function create(Department $department): View
    {
        $this->authorize('create', [DepartmentTransaction::class, $department]);

        return view('transactions.create', compact('department'));
    }

    public function store(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('create', [DepartmentTransaction::class, $department]);

        $data = $this->validateData($request);

        $department->transactions()->create([
            ...$data,
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('departments.transactions.index', $department)
            ->with('status', 'Muamala umeongezwa.');
    }

    public function edit(Department $department, DepartmentTransaction $transaction): View
    {
        $this->authorize('update', $transaction);
        abort_unless($transaction->department_id === $department->id, 404);

        return view('transactions.edit', compact('department', 'transaction'));
    }

    public function update(Request $request, Department $department, DepartmentTransaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        abort_unless($transaction->department_id === $department->id, 404);

        $transaction->update($this->validateData($request));

        return redirect()
            ->route('departments.transactions.index', $department)
            ->with('status', 'Muamala umesasishwa.');
    }

    public function destroy(Department $department, DepartmentTransaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        abort_unless($transaction->department_id === $department->id, 404);

        $transaction->delete();

        return redirect()
            ->route('departments.transactions.index', $department)
            ->with('status', 'Muamala umefutwa.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['required', 'date'],
        ]);
    }
}
