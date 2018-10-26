<?php

namespace App\Http\Controllers;

use App\User;
use App\Horario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
         * Esta laço de repetição abaixo insere alguns dados de testes, foi posto aqui apenas por comodidade,
         * já que a lógica montado para este caso não é um tipo de "teste oficial"
         *
        for ($diaEmSegundos=strtotime('2018-01-01 12:00:00'); $diaEmSegundos < strtotime('2019-01-01 12:00:00'); $diaEmSegundos+=86400) {
            $diaDaSemana = date('N', $diaEmSegundos);
            $numeroDoMes = intval(date('m', $diaEmSegundos));

            if ($diaDaSemana != '6' && $diaDaSemana != '7' && $numeroDoMes < 10) {
                Horario::create([
                    'data_hora' => date('Y-m-d 08:30:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'M',
                    'user_id' => '3'
                ]);
            }

            if ($diaDaSemana != '6' && $diaDaSemana != '7' && $numeroDoMes < 3) {
                Horario::create([
                    'data_hora' => date('Y-m-d 12:00:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'M',
                    'user_id' => '3'
                ]);

                Horario::create([
                    'data_hora' => date('Y-m-d 13:30:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'T',
                    'user_id' => '3'
                ]);

                Horario::create([
                    'data_hora' => date('Y-m-d 18:00:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'T',
                    'user_id' => '3'
                ]);
            }

            if ($diaDaSemana != '6' && $diaDaSemana != '7' && $numeroDoMes >= 3 && $numeroDoMes < 10) {
                Horario::create([
                    'data_hora' => date('Y-m-d 14:30:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'T',
                    'user_id' => '3'
                ]);
            }

            if ($diaDaSemana != '7' && $numeroDoMes == 10) {
                Horario::create([
                    'data_hora' => date('Y-m-d 9:00:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'M',
                    'user_id' => '3'
                ]);

                Horario::create([
                    'data_hora' => date('Y-m-d 21:00:00', $diaEmSegundos),
                    'tipo' => 'E',
                    'turno' => 'N',
                    'user_id' => '3'
                ]);
            }
        };*/

        $horasPorMes = auth()->user()->obterHorasTrabalhadasPorMes();
        $horasPorMes = '['.implode(',',$horasPorMes).']';

        if (auth()->user()->can('administrador')) {
            $usuarios = User::all();
        } else {
            $usuarios = null;
        }

        return view('home', compact(['horasPorMes', 'usuarios']));
    }

    public function gotohome()
    {
        /*
          Isto serve para caso o usuário digite diretamente o domínio do site.
          Caso você opte por não fazer esse redirecionamento, o Dashboard (home) irá funcionar normalmente, porém o menu
          não ficará com o item "Dashboard" ativo.
        */
        return redirect('/home');
    }
}
