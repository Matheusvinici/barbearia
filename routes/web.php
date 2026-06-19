<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BarbeiroController;
use App\Http\Controllers\Admin\ServicoController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\AgendamentoController;
use App\Http\Controllers\Admin\BloqueioController;
use App\Http\Controllers\Admin\DespesaController;
use App\Http\Controllers\Admin\CaixaController;
use App\Http\Controllers\Admin\RelatorioController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Barbeiro\AuthController as BarberAuthController;
use App\Http\Controllers\Barbeiro\DashboardController as BarberDashboardController;
use App\Http\Controllers\Barbeiro\AgendamentoController as BarberAgendamentoController;

Route::get('/', fn () => redirect()->route('site.login'));

Route::prefix('site')->name('site.')->group(function () {
    Route::get('/login', \App\Livewire\Site\LoginCliente::class)->name('login');
    Route::get('/agendar', \App\Livewire\Site\AgendarWizard::class)->name('agendar');
    Route::get('/meus-agendamentos', \App\Livewire\Site\MeusAgendamentos::class)->name('meus-agendamentos');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('barbeiros', BarbeiroController::class)->names([
            'index' => 'barbeiros.index',
            'create' => 'barbeiros.create',
            'store' => 'barbeiros.store',
            'show' => 'barbeiros.show',
            'edit' => 'barbeiros.edit',
            'update' => 'barbeiros.update',
            'destroy' => 'barbeiros.destroy',
        ]);

        Route::resource('servicos', ServicoController::class)->names([
            'index' => 'servicos.index',
            'create' => 'servicos.create',
            'store' => 'servicos.store',
            'show' => 'servicos.show',
            'edit' => 'servicos.edit',
            'update' => 'servicos.update',
            'destroy' => 'servicos.destroy',
        ]);

        Route::resource('clientes', ClienteController::class)->names([
            'index' => 'clientes.index',
            'create' => 'clientes.create',
            'store' => 'clientes.store',
            'show' => 'clientes.show',
            'edit' => 'clientes.edit',
            'update' => 'clientes.update',
            'destroy' => 'clientes.destroy',
        ]);
        Route::get('/clientes/search/ajax', [ClienteController::class, 'search'])->name('clientes.search');

        Route::get('/agendamentos', [AgendamentoController::class, 'index'])->name('agendamentos.index');
        Route::post('/agendamentos', [AgendamentoController::class, 'store'])->name('agendamentos.store');
        Route::get('/agendamentos/create', [AgendamentoController::class, 'create'])->name('agendamentos.create');
        Route::get('/agendamentos/{agendamento}', [AgendamentoController::class, 'show'])->name('agendamentos.show');
        Route::get('/agendamentos/{agendamento}/edit', [AgendamentoController::class, 'edit'])->name('agendamentos.edit');
        Route::put('/agendamentos/{agendamento}', [AgendamentoController::class, 'update'])->name('agendamentos.update');
        Route::delete('/agendamentos/{agendamento}', [AgendamentoController::class, 'destroy'])->name('agendamentos.destroy');
        Route::get('/agendamentos/horarios/disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])->name('agendamentos.horarios');


        Route::get('/bloqueios', [BloqueioController::class, 'index'])->name('bloqueios.index');
        Route::post('/bloqueios', [BloqueioController::class, 'store'])->name('bloqueios.store');
        Route::delete('/bloqueios/{bloqueio}', [BloqueioController::class, 'destroy'])->name('bloqueios.destroy');

        Route::get('/despesas', [DespesaController::class, 'index'])->name('despesas.index');
        Route::get('/despesas/create', [DespesaController::class, 'create'])->name('despesas.create');
        Route::post('/despesas', [DespesaController::class, 'store'])->name('despesas.store');
        Route::get('/despesas/{despesa}/edit', [DespesaController::class, 'edit'])->name('despesas.edit');
        Route::put('/despesas/{despesa}', [DespesaController::class, 'update'])->name('despesas.update');
        Route::delete('/despesas/{despesa}', [DespesaController::class, 'destroy'])->name('despesas.destroy');
        Route::patch('/despesas/{despesa}/toggle-pago', [DespesaController::class, 'togglePago'])->name('despesas.toggle-pago');

        Route::get('/caixa', [CaixaController::class, 'index'])->name('caixa.index');
        Route::get('/caixa/{caixa}', [CaixaController::class, 'show'])->name('caixa.show');
        Route::post('/caixa/abrir', [CaixaController::class, 'abrir'])->name('caixa.abrir');
        Route::post('/caixa/{caixa}/fechar', [CaixaController::class, 'fechar'])->name('caixa.fechar');

        Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('/relatorios/faturamento', [RelatorioController::class, 'faturamento'])->name('relatorios.faturamento');
        Route::get('/relatorios/servicos', [RelatorioController::class, 'servicos'])->name('relatorios.servicos');
        Route::get('/relatorios/faturamento/pdf', [RelatorioController::class, 'pdfFaturamento'])->name('relatorios.faturamento-pdf');

        Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::post('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
        Route::get('/configuracoes/qr-code', [ConfiguracaoController::class, 'qrCode'])->name('configuracoes.qr-code');
        Route::post('/configuracoes/pair', [ConfiguracaoController::class, 'pairBot'])->name('configuracoes.pair');
    });

    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/marcar-todas', [NotificationController::class, 'marcarTodas'])->name('notificacoes.marcar-todas');
    Route::post('/notificacoes/{id}/marcar-lida', [NotificationController::class, 'marcarLida'])->name('notificacoes.marcar-lida');
});

Route::prefix('barbeiro')->name('barbeiro.')->group(function () {
    Route::get('/login', [BarberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [BarberAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [BarberAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth:barbeiro'])->group(function () {
        Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/agendamentos', [BarberAgendamentoController::class, 'index'])->name('agendamentos.index');
        Route::put('/agendamentos/{agendamento}/confirmar', [BarberAgendamentoController::class, 'confirmar'])->name('agendamentos.confirmar');
        Route::put('/agendamentos/{agendamento}/realizar', [BarberAgendamentoController::class, 'realizar'])->name('agendamentos.realizar');
        Route::put('/agendamentos/{agendamento}/cancelar', [BarberAgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar');
    });
});
