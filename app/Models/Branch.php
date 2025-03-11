<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public static function generateBranchCode($branchName)
    {
        $code = strtoupper(substr($branchName, 0, 2));

        $existingCode = self::where('code', $code)->exists();

        if($existingCode) {
            $code .=  rand(10, 99);
        }

        return $code;
    }
}
