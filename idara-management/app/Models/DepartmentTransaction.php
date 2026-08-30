<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kumbukumbu za miamala/risiti za idara - DATA NYETI YA FEDHA. Angalia
 * architecture.md §2.6 na §5, na TransactionPolicy - hii ndiyo modeli pekee
 * ambayo "mwanachama wa kawaida" wa idara husika HAWEZI kuiona, ni
 * Kiongozi/Admin pekee.
 */
class DepartmentTransaction extends Model
{
    use BelongsToDepartment, HasFactory;

    protected $fillable = [
        'department_id',
        'type',
        'amount',
        'description',
        'recorded_by',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'date',
        ];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
