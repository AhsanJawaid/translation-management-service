<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TranslationTag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function translationKeys(): BelongsToMany
    {
        return $this->belongsToMany(TranslationKey::class);
    }
}