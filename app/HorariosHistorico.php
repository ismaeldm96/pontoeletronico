<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HorariosHistorico extends Model
{
    protected $table = 'horarios_historico';
    public $timestamps = false;
    protected $fillable = [
        'horarios_id', 'user_id', 'acao'
    ];

    public function users()
    {
        return dd($this->belongsTo('App\User', 'user_id')->toSql());

    }

    public function horarios()
    {
        return $this->belongsTo('App\Horario', 'horarios_id');
    }
}
