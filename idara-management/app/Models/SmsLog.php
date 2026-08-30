<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rekodi ya kitendo kimoja cha "tuma SMS" kwa idara husika - angalia
 * architecture.md §2.4. Global Scope (kupitia BelongsToDepartment) inahakikisha
 * mtumiaji haoni logi za idara asiyomo.
 */
class SmsLog extends Model
{
    use BelongsToDepartment;

    protected $fillable = [
        'department_id',
        'sent_by',
        'message',
        'recipients_count',
        'sent_count',
        'failed_count',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
