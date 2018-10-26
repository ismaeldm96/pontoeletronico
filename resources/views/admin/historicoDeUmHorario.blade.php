@forelse($historicos as $historico)
    [{{ date('m/d/Y h:i:s A', strtotime($historico->atualizado_em)) }}], <b>{{ $historico->acao }}</b>. <small>{{ $historico->users->name }}</small><br>
@empty
@endforelse