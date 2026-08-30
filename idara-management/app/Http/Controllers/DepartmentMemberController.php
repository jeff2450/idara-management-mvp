<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentMemberRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\MemberImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Kuongeza/kuondoa Kiongozi au Mwanachama kwenye idara,
 * na kuingiza wanachama wengi kwa mara moja kupitia faili la Excel/CSV.
 */
class DepartmentMemberController extends Controller
{
    public function store(StoreDepartmentMemberRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        $user = $data['mode'] === 'existing'
            ? User::where('email', $data['email'])->firstOrFail()
            : User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
            ]);

        $department->users()->syncWithoutDetaching([
            $user->id => ['role' => $data['role']],
        ]);

        $user->syncGlobalRoleFromDepartments();

        $roleLabel = $data['role'] === 'leader' ? 'Kiongozi' : 'Mwanachama';

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "{$user->name} ameongezwa kwenye '{$department->name}' kama {$roleLabel}.");
    }

    public function destroy(Department $department, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $department);

        $department->users()->detach($user->id);
        $user->syncGlobalRoleFromDepartments();

        return redirect()
            ->route('departments.show', $department)
            ->with('status', "{$user->name} ameondolewa kwenye '{$department->name}'.");
    }

    /**
     * Display the Excel/CSV bulk import form.
     */
    public function importForm(Department $department): View
    {
        $this->authorize('manageMembers', $department);

        return view('departments.members.import', [
            'department' => $department,
        ]);
    }

    /**
     * Handle the Excel/CSV bulk import.
     */
    public function import(Request $request, Department $department, MemberImportService $importService): RedirectResponse
    {
        $this->authorize('manageMembers', $department);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
            'role' => ['required', 'in:member,leader'],
        ], [
            'file.required' => 'Tafadhali chagua faili la Excel (.xlsx) au CSV (.csv).',
            'file.mimes' => 'Faili lazima liwe la aina ya Excel (.xlsx, .xls) au CSV (.csv, .txt).',
            'file.max' => 'Ukubwa wa faili usizidi 10MB.',
        ]);

        // Only admins can assign leaders
        $role = $request->input('role', 'member');
        if ($role === 'leader' && ! $request->user()->isAdmin()) {
            abort(403, 'Uteuzi wa Kiongozi ni Admin pekee.');
        }

        $result = $importService->import(
            $request->file('file'),
            $department,
            $role,
            $request->user()
        );

        if ($result['total'] === 0) {
            $errorMsg = ! empty($result['errors']) ? implode(' ', $result['errors']) : 'Hakuna mwanachama aliyepatikana kwenye faili.';
            return redirect()
                ->back()
                ->withErrors(['file' => $errorMsg]);
        }

        $message = "Wanachama {$result['total']} wameingizwa kikamilifu kwenye '{$department->name}' (Wapya: {$result['new']}, Waliokuwepo: {$result['existing']}).";
        if (! empty($result['errors'])) {
            $message .= ' ' . count($result['errors']) . ' mistari ilirukwa kwa sababu ya hitilafu.';
        }

        return redirect()
            ->route('departments.show', $department)
            ->with('status', $message);
    }

    /**
     * Download sample CSV/Excel template.
     */
    public function downloadTemplate(Department $department): StreamedResponse
    {
        $filename = "template-wanachama-{$department->slug}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel Swahili/Unicode compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV Header
            fputcsv($handle, ['Jina Kamili', 'Namba ya Simu', 'Barua Pepe']);

            // Write Sample Data
            fputcsv($handle, ['Michael John', '0712345678', 'michael.john@example.com']);
            fputcsv($handle, ['Neema Paul', '0714567890', 'neema.paul@example.com']);
            fputcsv($handle, ['David Peter', '0767123456', '']);
            fputcsv($handle, ['Bahati Grace', '0716789123', '']);
            fputcsv($handle, ['Emmanuel Eliu', '0754321987', 'emmanuel@example.com']);

            fclose($handle);
        }, 200, $headers);
    }
}
