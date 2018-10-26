@extends('adminlte::page')

@push('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {
                labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                datasets: [{
                    label: "Horas trabalhadas por mês em {{ date('Y') }}",
                    tension: 0,
                    backgroundColor: 'rgba(0, 166, 132, 0.5)',
                    borderColor: 'rgb(0, 166, 132)',
                    data: {!! $horasPorMes !!}
                }]
            },
            options: {
                tooltips: {
                    callbacks: {
                        label: function(tooltipItems, data) {
                            return " Horas trabalhadas: " + tooltipItems.yLabel;
                        }
                    }
                }
            }
        });
    </script>
@endpush

@section('title', 'PE - Dashboard')

@section('content_header')
    <ol class="breadcrumb">
        <li><a href="/home">Dashboard</a></li>
    </ol>
@stop

@section('content')

    @include('includes.alertas')
    {!! Form::open([
            'route' => 'horarios.store',
            'method' => 'post',
            'class' => 'form']) !!}
    <input type="hidden" name="routeRedirect" value="home" />
    <button type="submit" class="btn btn-success btn-lg" data-toggle="tooltip" data-placement="bottom" title="Será incluído um registro com a data e hora atual"><span class="glyphicon glyphicon-time"></span> Registrar meu ponto agora</button>
    {!! Form::close() !!}
    <div class="clearfix"></div>

    <br />
    <div class="panel panel-default">
        <div class="panel-heading">
            {!! Form::open([
                    'route' => 'home',
                    'method' => 'post',
                    'class' => 'form form-inline']) !!}

            {!! Form::label('visualizar', 'Visualizar :'); !!}
            {!! Form::select('visualizar', ['Semana (Não implementado)', 'Mês'], 1, [
                    'class' => 'form-control',
                    'name' => 'visualizar',
                    'id' => 'visualizar'
                 ]) !!}

            <label for="visualizar" style="float: right; padding-top: 6px">Você já registrou {{ explode(',', trim($horasPorMes,'[]'))[intval(date('m'))-1] }} horas neste mês</label>
            <div class="clearfix"></div>
            {!! Form::close() !!}
        </div>
        <div class="panel-body">
            <canvas id="myChart"></canvas>
        </div>
    </div>

    @if (auth()->user()->can('administrador'))
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Horas trabalhadas neste mês (até o momento) dos usuários registrados</h4>
        </div>
        <div class="panel-body">
            @if (isset($usuarios) && count($usuarios) > 0)
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Horas trabalhadas em {{ date('M') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->obterHorasTrabalhadasNoMes() }}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            @else
                <h4 align="center">Nenhum usuário encontrado, mas então como você chegou aqui?</h4>
            @endif
        </div>
    </div>
    @endif
@stop