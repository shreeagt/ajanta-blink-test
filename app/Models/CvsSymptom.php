<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvsSymptom extends Model
{
    protected $fillable = ['symptom_key', 'sort_order', 'lang_code', 'symptom_text'];

    /**
     * Get all symptoms grouped by lang_code.
     */
    public static function getAllGrouped(): array
    {
        $all = static::orderBy('sort_order')->get(['lang_code', 'symptom_key', 'symptom_text']);
        $grouped = [];
        foreach ($all as $row) {
            $grouped[$row->lang_code][] = [
                'id' => $row->symptom_key,
                'label' => $row->symptom_text
            ];
        }
        return $grouped;
    }
}
