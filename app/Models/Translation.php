<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['translation_key_id', 'translation_group_id', 'language_code', 'value'];

    public function key()
    {
        return $this->belongsTo(TranslationKey::class, 'translation_key_id');
    }

    public function group()
    {
        return $this->belongsTo(TranslationGroup::class, 'translation_group_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }
}