<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Database\Factories\AnnualScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kipengele kimoja cha Ratiba ya Mwaka - prd.md §5.3, architecture.md §2.7.
 * Inatumia trait ile ile ya `BelongsToDepartment` inayotumiwa na SmsLog n.k.
 * (angalia app/Models/Concerns/BelongsToDepartment.php) - hii inaongeza
 * Global Scope kiotomatiki ili idara moja isione ratiba ya nyingine.
 */
class AnnualSchedule extends Model
{
    /** @use HasFactory<AnnualScheduleFactory> */
    use BelongsToDepartment, HasFactory;

    protected $fillable = [
        'department_id',
        'created_by',
        'title',
        'description',
        'planned_year',
        'planned_month',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'planned_year' => 'integer',
            'planned_month' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /** Inaitwa na ActivityLogController pale shughuli inaporekodiwa dhidi ya ratiba hii. */
    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
