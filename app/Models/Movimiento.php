<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $table = 'movimientos';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = [
        'icon',
        'user_id',
        'descripcion',
        'valor',
        'category_id',
        'tipo'
    ];
    public function getCreatedAtAttribute($date){
        return Carbon::parse($date)
        ->timezone('America/Asuncion')
        ->format('Y-m-d H:i:s');
    }
}
