@extends('adminlte::page')

@section('title', 'PE - Histórico de alterações')

@section('content_header')
    <ol class="breadcrumb">
        <li><a href="/home">Dashboard</a></li>
        <li><a href="/horarios">Horários</a></li>
        <li><a href="/horarios/historico">Histórico</a></li>
    </ol>
@stop

@section('content')
    @include('includes.alertas')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>O histórico está agrupado pela data do registro do ponto (mais recente para a mais antiga)</h4>
        </div>
        <div class="panel-body">
            @if (isset($historicos) && count($historicos) > 0)
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID do Ponto</th>
                        <th>Horário registrado no ponto</th>
                        <th>Ação tomada</th>
                        <th>Data da ação</th>
                        <th>Usuário que fez a ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    {!! $dia = null !!}
                    @forelse($historicos as $historico)
                        <?php
                            $dataEmSegundos = strtotime($historico->data_hora);
                            $diaIteracao = date('d', $dataEmSegundos);
                            if ($dia != $diaIteracao) {
                                $dia = $diaIteracao;
                                echo '<tr><td colspan="6" class="success"><b> • Pontos do dia '.date('m/d/Y', $dataEmSegundos).' • </b></td></tr>';
                            }
                        ?>
                        <tr>
                            <td>{{ $historico->id }}</td>
                            <td>{{ $historico->horarios_id }}</td>
                            <td>{{ date('m/d/Y h:i:s A', $dataEmSegundos) }}</td>
                            <td>{{ $historico->acao }}</td>
                            <td>{{ date('m/d/Y h:i:s A', strtotime($historico->atualizado_em)) }}</td>
                            <td>{{ $historico->name }}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            @else
                <h4 align="center">Nenhum registro encontrado</h4>
            @endif

            @if (isset($historicos))
                {!! $historicos->links() !!}
            @endif
        </div>
    </div>
@stop