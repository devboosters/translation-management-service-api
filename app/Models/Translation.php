<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Translation",
 *     type="object",
 *     title="Translation",
 *     required={"group", "key", "value", "locale"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="group", type="string", example="validation"),
 *     @OA\Property(property="key", type="string", example="required"),
 *     @OA\Property(property="value", type="string", example="This field is required"),
 *     @OA\Property(property="locale", type="string", example="en"),
 *     @OA\Property(property="tag", type="string", example="web", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'locale',
        'tag'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
