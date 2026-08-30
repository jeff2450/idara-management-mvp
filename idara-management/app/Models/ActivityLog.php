<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "Kilichofanyika halisi" - dhidi ya ratiba (AnnualSchedule) au huru.
 * prd.md §4.6/§4.7, architecture.md §2.7.
 */
class ActivityLog extends Model
{
    use BelongsToDepartment, HasFactory;

    protected $fillable = [
        'department_id',
        'annual_schedule_id',
        'recorded_by',
        'title',
        'description',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'date',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AnnualSchedule::class, 'annual_schedule_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
