<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Horario;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function horarios()
    {
        if (auth()->user()->can('administrador')) {
            return $this->hasMany(Horario::class);
        } else {
            return $this->hasMany(Horario::class)->where(['ativo' => true]);
        }
    }

    /**
     * @param null $id
     * @return mixed
     */

    public function obterUsuarios()
    {
        if (auth()->user()->can('administrador')) {
            $usuarios = [];
            $usuariosQuery = User::all(['id', 'name'])->toArray();
            foreach ($usuariosQuery as $usuarioQuery) {
                $usuarios[$usuarioQuery['id']] = $usuarioQuery['name'];
            }

            return $usuarios;
        }

        return null;
    }

    public function obterCreatedAtFormatado()
    {
        if ($this->created_at)
            return date('m/d/Y h:i:s A', strtotime($this->created_at));

        return null;
    }

    public function obterHorasTrabalhadasPorMes($id = null)
    {
        if (!is_numeric($id))
            $id = $this->id;

        $horarios = Horario::where(function($query) use ($id) {
            $query->where('user_id', '=', $id);
            $query->where('ativo', '=', true);
            $query->where(DB::raw('year(data_hora)'), '=', date('Y'));
        })->orderBy('data_hora')->get();

        $horasPorMes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $segundosMes = 0;
        $iteracao = 0;
        $datahoraTemp = null;
        $darahoraIteracao = null;
        $mesIteracao = null;
        $mesTemp = 0;
        foreach($horarios as $horario) {
            $mesIteracao = intval(date('m', $darahoraIteracao))-1;
            if ($mesIteracao != $mesTemp) {
                $horasPorMes[$mesTemp] = number_format($segundosMes / 3600, 2, '.', '');
                $mesTemp = $mesIteracao;
                $segundosMes = 0;
            }

            $darahoraIteracao = strtotime($horario->data_hora);
            if (++$iteracao%2 == 0) {
                // Faz o calculo com a hora deste registro e com a hora do registro anterior
                $segundosMes += $darahoraIteracao - $datahoraTemp;
            } else {
                //Guarda a hora deste registro
                $datahoraTemp = $darahoraIteracao;
            }
        }

        $horasPorMes[$mesIteracao] = number_format($segundosMes / 3600, 2, '.', '');
        return $horasPorMes;
    }

    public function obterHorasTrabalhadasNoMes($id = null)
    {
        if (!is_numeric($id))
            $id = $this->id;

        $horarios = Horario::where(function($query) use ($id) {
            $query->where('user_id', '=', $id);
            $query->where('ativo', '=', true);
            $query->where(DB::raw('month(data_hora)'), '=', DB::raw('month(now())'));
        })->orderBy('data_hora')->get();

        $segundosMes = 0;
        $iteracao = 0;
        $datahoraTemp = null;
        $darahoraIteracao = null;
        foreach($horarios as $horario) {
            $darahoraIteracao = strtotime($horario->data_hora);
            if (++$iteracao%2 == 0) {
                // Faz o calculo com a hora deste registro e com a hora do registro anterior
                $segundosMes += $darahoraIteracao - $datahoraTemp;
            } else {
                //Guarda a hora deste registro
                $datahoraTemp = $darahoraIteracao;
            }
        }

        $horasNoMes = number_format($segundosMes / 3600, 2, '.', '');
        return $horasNoMes;
    }
}
