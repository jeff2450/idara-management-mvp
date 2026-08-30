<?php

namespace App\Http\Controllers;

use App\Jobs\SendDepartmentSms;
use App\Models\Department;
use App\Models\SmsLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Angalia architecture.md §2.4 na SRS §4.4 - SMS ya kulengwa kwa wanachama
 * wa idara husika pekee.
 */
class SmsController extends Controller
{
    public function index(Department $department): View
    {
        $this->authorize('viewAny', [SmsLog::class, $department]);

        $logs = $department->smsLogs()->with('sender')->latest('sent_at')->paginate(15);

        return view('sms.index', compact('department', 'logs'));
    }

    public function create(Department $department): View
    {
        $this->authorize('create', [SmsLog::class, $department]);

        $recipients = $department->users()->orderBy('name')->get();

        return view('sms.create', compact('department', 'recipients'));
    }

    public function store(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('create', [SmsLog::class, $department]);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:459'], // ~3 SMS pages
            'recipient_ids' => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer'],
        ]);

        // Thibitisha recipient_ids zote ni za idara hii pekee - ulinzi wa
        // ziada kabla ya kufikisha kwenye job ya queue.
        $validRecipientIds = $department->users()
            ->whereIn('users.id', $data['recipient_ids'])
            ->pluck('users.id')
            ->all();

        SendDepartmentSms::dispatch(
            departmentId: $department->id,
            sentByUserId: $request->user()->id,
            message: $data['message'],
            recipientUserIds: $validRecipientIds,
        );

        return redirect()
            ->route('departments.sms.index', $department)
            ->with('status', 'Ujumbe umewekwa kwenye foleni ya kutuma kwa '.count($validRecipientIds).' wapokeaji.');
    }
}
