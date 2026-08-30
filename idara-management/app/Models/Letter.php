<?php

namespace App\Models;

use App\Models\Concerns\BelongsToDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Barua halisi iliyozalishwa kwa idara husika - angalia architecture.md §2.5.
 */
class Letter extends Model
{
    use BelongsToDepartment;

    protected $fillable = [
        'department_id',
        'template_id',
        'generated_by',
        'recipient_name',
        'file_path',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class, 'template_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
