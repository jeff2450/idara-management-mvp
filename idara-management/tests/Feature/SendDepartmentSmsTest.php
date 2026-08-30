<?php

namespace Tests\Feature;

use App\Jobs\SendDepartmentSms;
use App\Models\Department;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\Sms\SmsGatewayInterface;
use App\Services\Sms\SmsSendResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SRS §4.4: "Ujumbe usifike kwa wanachama wa idara nyingine - utumaji uwe
 * targeted, siyo broadcast ya jumla." Hii inathibitisha kwamba job yenyewe
 * (siyo controller tu) inachuja recipients dhidi ya department_user - angalia
 * maelezo kwenye SendDepartmentSms::handle().
 */
class SendDepartmentSmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_sends_to_members_of_the_given_department(): void
    {
        $vijana = Department::factory()->create();
        $wamama = Department::factory()->create();

        $vijanaMember = User::factory()->create(['phone' => '0712345678']);
        $vijana->users()->attach($vijanaMember->id, ['role' => 'member']);

        $wamamaMember = User::factory()->create(['phone' => '0755555555']);
        $wamama->users()->attach($wamamaMember->id, ['role' => 'member']);

        $sender = User::factory()->create();

        $sentTo = [];

        $this->mock(SmsGatewayInterface::class, function ($mock) use (&$sentTo) {
            $mock->shouldReceive('send')->andReturnUsing(function (string $phone, string $message) use (&$sentTo) {
                $sentTo[] = $phone;

                return SmsSendResult::success();
            });
        });

        // Jaribu "kudukua" kwa kuingiza recipient_id ya idara nyingine pia -
        // job inatakiwa kuipuuza kwa sababu si mwanachama wa $vijana.
        (new SendDepartmentSms(
            departmentId: $vijana->id,
            sentByUserId: $sender->id,
            message: 'Habari za mkutano wa kesho',
            recipientUserIds: [$vijanaMember->id, $wamamaMember->id],
        ))->handle(app(SmsGatewayInterface::class));

        $this->assertSame(['0712345678'], $sentTo);

        $this->assertDatabaseHas('sms_logs', [
            'department_id' => $vijana->id,
            'recipients_count' => 1,
            'sent_count' => 1,
            'status' => 'sent',
        ]);
    }
}
