<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Angalia architecture.md §2.4 na §4.A - Job hii inatuma SMS kwa batch ya
 * wanachama WA IDARA HUSIKA PEKEE. Recipient IDs zinathibitishwa dhidi ya
 * department_user tena hapa ndani ya job (siyo kutegemea tu udhibiti wa
 * controller) - hii ni ulinzi wa ziada dhidi ya ujumbe "kuvuja" nje ya idara
 * hata kama job ingeitwa kimakosa na recipient_id isiyo sahihi.
 */
class SendDepartmentSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<int>  $recipientUserIds
     */
    public function __construct(
        public readonly int $departmentId,
        public readonly int $sentByUserId,
        public readonly string $message,
        public readonly array $recipientUserIds,
    ) {}

    public function handle(SmsGatewayInterface $gateway): void
    {
        $department = Department::withoutGlobalScopes()->findOrFail($this->departmentId);

        // Chuja recipients dhidi ya department_user - hii ndiyo "targeted,
        // siyo broadcast ya jumla" iliyotajwa kwenye SRS §4.4.
        $recipients = $department->users()
            ->whereIn('users.id', $this->recipientUserIds)
            ->whereNotNull('phone')
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $result = $gateway->send($recipient->phone, $this->message);

            $result->delivered ? $sentCount++ : $failedCount++;
        }

        $status = match (true) {
            $sentCount === 0 => 'failed',
            $failedCount === 0 => 'sent',
            default => 'partially_sent',
        };

        SmsLog::create([
            'department_id' => $this->departmentId,
            'sent_by' => $this->sentByUserId,
            'message' => $this->message,
            'recipients_count' => $recipients->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'status' => $status,
            'sent_at' => now(),
        ]);
    }
}
