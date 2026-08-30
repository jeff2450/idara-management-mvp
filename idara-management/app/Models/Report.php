<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ripoti ya PDF iliyozalishwa na `report:generate` (au ombi la moja kwa
 * moja la kiongozi) - angalia app/Console/Commands/GenerateDepartmentReports.php.
 */
class Report extends Model
{
    use BelongsToDepartment;

    protected $fillable = [
        'department_id',
        'generated_by',
        'period',
        'file_path',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
