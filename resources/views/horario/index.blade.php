<?php
function includeAsJsString($template)
{
    $string = view($template);
    return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
}
?>

@extends('adminlte::page')

@push('css')
    <link rel="stylesheet" href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/i18n/defaults-pt_BR.js"></script>
    <script type="text/javascript" src="/bower_components/moment/min/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#dtpNovoRegistro').datetimepicker();
            $('#dtpSearchMin').datetimepicker();
            $('#dtpSearchMax').datetimepicker({
                useCurrent: false //Important! See issue #1075
            });
            $("#dtpSearchMin").on("dp.change", function (e) {
                $('#dtpSearchMax').data("DateTimePicker").minDate(e.date);
            });
            $("#dtpSearchMax").on("dp.change", function (e) {
                $('#dtpSearchMin').data("DateTimePicker").maxDate(e.date);
            });
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function(){
            $('.selectpicker').selectpicker();
        });

        function esconderHistorico(e)
        {
            var id = $(e).parent().parent().parent().children(1).html();
            $(e).attr('onclick', 'visualizarHistorico(this)');
            $('#historicoDoHorario'+id).slideUp(250, function(){
                $('#historicoDoHorario'+id).parent().parent().remove();
            })
        }

        function visualizarHistorico(e)
        {
            $(e).attr('onclick', 'esconderHistorico(this)');
            var registro = $(e).parent().parent().parent();
            var id = $(registro).children(1).html();

            $(registro).after("<tr><td colspan='5'><div id='historicoDoHorario"+id+"'>{!! includeAsJsString('includes.loader') !!}<div></td></tr>");
            $.ajax({
                url: '/historico/horarios/' + id,

                success: function(data){
                    $('#historicoDoHorario'+id).html('<div id="conteudoHistorico'+id+'" style="display: none">' + data + '</div>');
                    $('#conteudoHistorico'+id).fadeIn(500);
                },

                complete: function(){
                    $('.loader').remove();
                }
            });
        }
    </script>
@endpush

@section('title', 'PE - Horários')

@section('content_header')
    <ol class="breadcrumb">
        <li><a href="/home">Dashboard</a></li>
        <li><a href="/home/horarios">Horários</a></li>
    </ol>
@stop

@section('content')
    @include('includes.alertas')
    {!! Form::open([
            'route' => 'horarios.store',
            'method' => 'post',
            'class' => 'form form-inline']) !!}

    {!! Form::label('datahora', 'Registrar novo horário'); !!}
    <br>
    <div class="form-group">
        <div class='input-group date' id='dtpNovoRegistro'>
            <input type='text' class="form-control" name="datahora" id="datahora" placeholder="Data de registro do Ponto" required />
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
    </div>
    {!! Form::submit('Registrar', ['class' => 'btn btn-success']); !!}
    {!! Form::close() !!}

    <br />
    <div class="panel panel-default">
        <div class="panel-heading">
            {!! Form::open([
                    'route' => 'horarios.search',
                    'method' => 'post',
                    'class' => 'form form-inline form-pesquisa']) !!}

            @if (isset($usuarios) && auth()->user()->can('administrador'))
                {!! Form::label('usuario', 'Usuário:'); !!}
                <div class="form-group">
                    {!! Form::select(
                            'Usuario',
                            $usuarios,
                            (isset($pesquisa) && (isset($pesquisa['usuario']))) ? $pesquisa['usuario'] : '',
                            [
                                'class' => 'selectpicker show-tick',
                                'name' => 'usuario',
                                'id' => 'usuario',
                                'data-live-search' => 'true',
                                'data-width' => 'fit'
                            ]
                        )
                     !!}
                </div>
            @endif

            {!! Form::label('datainicial', 'Data Inicial:'); !!}
            <div class="form-group">
                <div class='input-group date' id='dtpSearchMin'>
                    <input type='text' class="form-control" name="datainicial" id="datainicial" value="{{ (isset($pesquisa) && (isset($pesquisa['datainicial']))) ? $pesquisa['datainicial'] : '' }}" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>

            {!! Form::label('datafinal', 'Data Final:'); !!}
            <div class="form-group">
                <div class='input-group date' id='dtpSearchMax'>
                    <input type='text' class="form-control" name="datafinal" id="datafinal" value="{{ (isset($pesquisa) && (isset($pesquisa['datafinal']))) ? $pesquisa['datafinal'] : '' }}" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>

            {!! Form::label('turno', 'Turno:'); !!}
            <div class="form-group">
                {!! Form::select(
                        'Turno',
                        $turnos,
                        (isset($pesquisa) && (isset($pesquisa['turno']))) ? $pesquisa['turno'] : '',
                        [
                            'class' => 'selectpicker show-tick',
                            'name' => 'turno',
                            'id' => 'turno',
                            'data-width' => 'fit'
                        ]
                    )
                !!}
            </div>

            <button type="submit" class="btn btn-link">
                <span class="glyphicon glyphicon-search"></span> Pesquisar
            </button>
            {!! Form::close() !!}
        </div>
        <div class="panel-body">
            @if (isset($horarios) && count($horarios) > 0)
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        @if (auth()->user()->can('administrador'))
                            <th>Usuário</th>
                        @endif
                        <th>Data e Hora</th>
                        <th>Turno</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $horario)
                            <tr class="{{ $horario->ativo ? '' : 'registro-deletado' }}">
                                <td>{{ $horario->id }}</td>
                                @if (auth()->user()->can('administrador'))
                                    @if ($horario->users)
                                        <td>{{ $horario->users->name }}</td>
                                    @else
                                        <td> - </td>
                                    @endif
                                @endif
                                <td>{{ $horario->obterDataHoraFormatada() }}</td>
                                <td>{{ $horario->obterTurnoFormatado() }}</td>
                                <td align="center" style="text-decoration: none;">
                                    @if ($horario->ativo)
                                        {!! Form::open([
                                                'route' => array('horarios.destroy', $horario->id),
                                                'method' => 'delete']) !!}
                                        <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Excluir registro"><span class="glyphicon glyphicon-trash"></span></button>
                                    @else
                                        {!! Form::open([
                                                'route' => array('horarios.restore', $horario->id),
                                                'method' => 'post']) !!}
                                        <button type="submit" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="top" title="Restaurar registro"><span class="glyphicon glyphicon-ok"></span></button>
                                    @endif
                                    @if (auth()->user()->can('administrador'))
                                        <button type="button" onclick="visualizarHistorico(this)" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="Visualizar histórico"><span class="glyphicon glyphicon-time"></span></button>
                                    @endif
                                    <button type="button" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Editar registro (Não está implementado)"><span class="glyphicon glyphicon-edit"></span></button>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            @else
                <h4 align="center">Nenhum registro encontrado</h4>
            @endif

            @if (isset($horarios))
                @if (isset($pesquisa))
                    {!! $horarios->appends($pesquisa)->links() !!}
                @else
                    {!! $horarios->links() !!}
                @endif
            @endif
        </div>
    </div>
@stop