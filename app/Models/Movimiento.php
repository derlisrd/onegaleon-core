<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $fillable = [
        'icon',
        'user_id',
        'descripcion',
        'valor',
        'category_id'
    ];
    public function getFormattedCreatedAtAttribute(){
        return Carbon::parse($this->created_at)->format('Y-m-d H:i');
    }
}
