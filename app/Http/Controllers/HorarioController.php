<?php

namespace App\Http\Controllers;

use DB;
use App\Horario;
use App\HorariosHistorico;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('administrador')) {
            $horarios = auth()->user()->horarios()->with(['users'])->orderBy('id', 'desc')->paginate(10);
        } else {
            $horarios = auth()->user()->horarios()->orderBy('id', 'desc')->paginate(10);
        }
        $turnos = Horario::turnos;
        $usuarios = auth()->user()->obterUsuarios();
        $usuarios[''] = 'Todos';
        $pesquisa['usuario'] = auth()->user()->id;

        return view('horario.index', compact('horarios', 'turnos', 'usuarios', 'pesquisa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->routeRedirect)) {
            $routeRedirect = $request->routeRedirect;
        } else {
            $routeRedirect = 'horarios';
        }

        if (isset($request->datahora)) {
            $datahora = $request->datahora;
        } else {
            $datahora = date('Y-m-d H:i:s');
        }

        $response = auth()->user()->horarios()->firstOrNew([])->horarioStore($datahora);
        if ($response['success']) {
            return redirect()->route($routeRedirect)->with('success', $response['message']);
        } else {
            return redirect()->route($routeRedirect)->with('error', $response['message']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Horario  $horario
     * @return \Illuminate\Http\Response
     */
    public function show(Horario $horario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Horario  $horario
     * @return \Illuminate\Http\Response
     */
    public function edit(Horario $horario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Horario  $horario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Horario $horario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Horario  $horario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        $horario = Horario::where('id', $id)->first();
        if ($horario) {
            $salvo = HorariosHistorico::create([
                'horarios_id' => $id,
                'user_id' => auth()->user()->id,
                'acao' => 'Registro foi inativado (deletado)'
            ]);

            if ($salvo) {
                $horario->ativo = false;
                $salvo = $horario->save();
            }

            if ($salvo) {
                DB::commit();
                return redirect()->route('horarios')->with('success', 'Registro excluído com sucesso');
            }
        }

        DB::rollback();
        return redirect()->route('horarios')->with('error', 'Erro ao excluir o registro');
    }

    public function restore($id)
    {
        DB::beginTransaction();
        $horario = Horario::find($id);
        if ($horario) {
            $salvo = HorariosHistorico::create([
                'horarios_id' => $id,
                'user_id' => auth()->user()->id,
                'acao' => 'Registro restaurado'
            ]);

            if ($salvo) {
                $horario->ativo = true;
                $salvo = $horario->save();
            }

            if ($salvo) {
                DB::commit();
                return redirect()->route('horarios')->with('success', 'Registro restaurado com sucesso');
            }
        }

        DB::rollback();
        return redirect()->route('horarios')->with('error', 'Erro ao restaurar o registro');
    }

    public function search(Request $request, Horario $horario)
    {
        $pesquisa = $request->except('_token');
        $horarios = $horario->search($pesquisa);
        $turnos = Horario::turnos;
        $usuarios = auth()->user()->obterUsuarios();
        $usuarios[''] = 'Todos';

        return view('horario.index', compact('horarios', 'turnos', 'usuarios', 'pesquisa'));
    }
}
