<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTranslation extends Model
{
    protected $fillable = ['lang_code', 'page', 'key', 'value'];

    /**
     * Get all translations grouped by lang_code and page.
     */
    public static function getAll(): array
    {
        $all = static::get(['lang_code', 'page', 'key', 'value']);
        $grouped = [];
        foreach ($all as $row) {
            $grouped[$row->lang_code][$row->page][$row->key] = $row->value;
        }
        return $grouped;
    }
}
