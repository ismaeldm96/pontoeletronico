<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    public $timestamps = false;
    protected $fillable = [
        'data_hora', 'tipo', 'turno', 'user_id'
    ];

    const turnos = [
        ''  => 'Turno',
        'M' => 'Manhã',
        'T' => 'Tarde',
        'N' => 'Noite'
    ];

    public function horarioStore($datahora)
    {
        $datahora = strtotime($datahora);
        $hora = date('H', $datahora);
        if ($hora >= 6 && $hora <= 12) {
            $turno = 'M';
        } elseif ($hora <= 18) {
            $turno = 'T';
        } else {
            $turno = 'N';
        };

        $salvo = Horario::create([
            'data_hora' => date('Y-m-d H:i:s', $datahora),
            'tipo'      => 'E',
            'turno'     => $turno,
            'user_id'   => auth()->user()->id
        ]);

        if ($salvo) {
            return [
                'success' => true,
                'message' => 'Seu horário foi registrado com sucesso'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Falha ao registrar seu horário'
            ];
        }
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function obterDataHoraFormatada()
    {
        if ($this->data_hora)
            return date('m/d/Y h:i:s A', strtotime($this->data_hora));

        return null;
    }

    public function obterTurnoFormatado()
    {
        if ($this->turno)
            return Horario::turnos[$this->turno];

        return null;
    }

    public function search($valores)
    {
        return $this->where(function($query) use ($valores) {
            if (auth()->user()->can('administrador')) {
                if (isset($valores['usuario'])) {
                    $query->where('user_id', '=', $valores['usuario']);
                }
            } else {
                $query->where('user_id', '=', auth()->user()->id);
                $query->where('ativo', '=', true);
            }

            if (isset($valores['datainicial']))
                $query->where('data_hora', '>=', date('Y-m-d H:i:s', strtotime($valores['datainicial'])));

            if (isset($valores['datafinal']))
                $query->where('data_hora', '<=', date('Y-m-d H:i:s', strtotime($valores['datafinal'])));

            if (isset($valores['turno']))
                $query->where('turno', '=', $valores['turno']);
        })->with('users')->orderBy('id', 'desc')->paginate(10);
    }
}
