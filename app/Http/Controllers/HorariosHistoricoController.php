<?php

namespace App\Http\Controllers;

use App\HorariosHistorico;
use Illuminate\Http\Request;
use DB;

class HorariosHistoricoController extends Controller
{
    public function index()
    {
        /* Infelizmente o orderBy neste caso abaixo não funciona...
        $historicos = HorariosHistorico::with(['horarios' => function ($query) {
                $query->orderBy('data_hora', 'desc');
            }, 'users'])
            ->paginate(15);
        */

        $historicos = DB::table('horarios_historico')
                        ->join('horarios', 'horarios.id', '=', 'horarios_historico.horarios_id')
                        ->join('users', 'users.id', '=', 'horarios_historico.user_id')
                        ->orderBy('horarios.data_hora', 'desc')
                        ->select('horarios_historico.id',
                                 'horarios_historico.horarios_id',
                                 'horarios.data_hora',
                                 'horarios_historico.acao',
                                 'horarios_historico.atualizado_em',
                                 'users.name')
                        ->paginate(20);

        return view('admin.historicohorario', compact('historicos'));
    }

    public function obterHistoricos($id_horario)
    {
        $historicos = HorariosHistorico::with(['users'])->where('horarios_id', $id_horario)->get();
        return view('admin.historicoDeUmHorario', compact('historicos'));
    }
}
