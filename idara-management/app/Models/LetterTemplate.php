<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Templates za barua - hizi ni za JUMLA (siyo scoped kwa idara), zinasimamiwa
 * na Admin, zinatumiwa na kiongozi yeyote - angalia architecture.md §2.5 na
 * maelezo kwenye migration ya create_letter_templates_table.
 */
class LetterTemplate extends Model
{
    protected $fillable = [
        'name',
        'body_template',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'template_id');
    }

    /**
     * Placeholders zinazotumika ndani ya template hii, mfano
     * ['jina_mwanachama', 'idara', 'tarehe'] - kwa ajili ya kuonyesha fomu
     * sahihi ya kujaza data kwenye LetterController@create.
     */
    public function placeholders(): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $this->body_template, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }
}
