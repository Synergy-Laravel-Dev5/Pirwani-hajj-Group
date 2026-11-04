<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('expense_view');
        $expenses = Expense::withCount('transactions')
            ->withSum('transactions as total_amount', 'amount')
            ->latest()
            ->get();

        $trashCount = Expense::onlyTrashed()->count();

        return view('expense.index', compact('expenses', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('expense_create');
        return view('expense.create');
    }

    public function store(Request $request)
    {
        $this->authorize('expense_create');
        $request->validate([
            'name' => 'required|string|max:255|unique:expenses,name',
        ]);

        $expense = Expense::create($request->only(['name']));

        logUserActivity('Expense Head Created', 'Name: ' . $expense->name, $expense->id, 'Expense');

        return redirect()->route('expense.index')
            ->with('success', 'Expense head created successfully.');
    }

    public function edit($id)
    {
        $this->authorize('expense_edit');
        $expense = Expense::findOrFail($id);
        return view('expense.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('expense_edit');
        $expense = Expense::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:expenses,name,' . $id,
        ]);

        $oldName = $expense->name;
        $expense->update($request->only(['name']));

        logUserActivity('Expense Head Updated', "Name changed from '{$oldName}' to '{$expense->name}'", $expense->id, 'Expense');

        return redirect()->route('expense.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorize('expense_trash');
        $expense     = Expense::findOrFail($id);
        $expenseName = $expense->name;

        logUserActivity('Expense Head Deleted', 'Moved to trash: ' . $expenseName, $expense->id, 'Expense');

        $expense->delete();

        return redirect()->route('expense.index')
            ->with('success', 'Expense moved to trash.');
    }

    public function trash()
    {
        $this->authorize('expense_trash_view');
        $expenses = Expense::onlyTrashed()->latest()->get();
        return view('expense.trash', compact('expenses'));
    }

    public function restore($id)
    {
        $this->authorize('expense_restore');
        $expense = Expense::onlyTrashed()->findOrFail($id);
        $expenseName = $expense->name;

        $expense->restore();

        logUserActivity('Expense Head Restored', 'Restored from trash: ' . $expenseName, $expense->id, 'Expense');

        return redirect()->route('expense.index')
            ->with('success', 'Expense restored successfully.');
    }
}
