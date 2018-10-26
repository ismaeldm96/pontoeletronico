@extends('adminlte::page')

@push('js')
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush

@section('title', 'PE - Usuários')

@section('content_header')
    <ol class="breadcrumb">
        <li><a href="/home">Dashboard</a></li>
        <li><a href="/usuarios">Usuários</a></li>
    </ol>
@stop

@section('content')
    @include('includes.alertas')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Usuários</h4>
        </div>
        <div class="panel-body">
            @if (isset($usuarios) && count($usuarios) > 0)
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Registrado em</th>
                        <th>Administrador</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->obterCreatedAtFormatado() }}</td>
                            <td>
                                {!! Form::open([
                                        'route' => array('usuarios.toggleAdmin', $usuario->id),
                                        'method' => 'post']) !!}

                                @if ($usuario->admin)
                                    {!! Form::submit('Remover privilégios', ['class' => 'btn btn-default btn-block btn-xs', ($usuario->name == 'admin') ? 'disabled' : '']); !!}
                                @else
                                    {!! Form::submit('Tornar administrador', ['class' => 'btn btn-primary btn-block btn-xs']); !!}
                                @endif
                                {!! Form::close() !!}
                            </td>
                            <td align="center">
                                {!! Form::open([
                                        'route' => array('usuarios.destroy', $usuario->id),
                                        'method' => 'delete']) !!}

                                <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Excluir usuário" {{ ($usuario->name == 'admin') ? 'disabled' : '' }}><span class="glyphicon glyphicon-trash"></span></button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            @else
                <h4 align="center">Nenhum usuário encontrado, mas então como você chegou aqui?</h4>
            @endif

            @if (isset($usuarios))
                {!! $usuarios->links() !!}
            @endif
        </div>
    </div>
@stop