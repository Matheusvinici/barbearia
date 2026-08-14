@extends('layouts.app')

@section('title', 'Nova Venda')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-receipt"/></svg>
    <span class="sep">/</span>
    <span>Vendas</span>
    <span class="sep">/</span>
    <span class="current">Nova Venda</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Comanda do cliente</span>
    <span class="pipe">·</span>
    <span>adicione os produtos comprados e o total é somado na comanda</span>
@endsection

@section('content')
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-receipt" viewBox="0 0 24 24" fill="none"><path d="M4 5c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v15.5l-2.5-1.5L15 20.5 12.5 19 10 20.5 7.5 19 5 20.5 4 19.5V5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 8h8M8 11.5h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-arrow-left" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5h.01M7.5 13.5h4.49" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-box" viewBox="0 0 24 24" fill="none"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M3.29 7L12 12l8.71-5M12 22V12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    </defs>
</svg>

@include('admin.vendas._form')

@endsection