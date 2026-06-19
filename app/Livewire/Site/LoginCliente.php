<?php

namespace App\Livewire\Site;

use App\Models\Cliente;
use Livewire\Component;

class LoginCliente extends Component
{
    public $step = 'phone';
    public $telefone = '';
    public $nome = '';
    public $error = '';

    public function mount()
    {
        if (session('cliente_id')) {
            $this->redirect(route('site.agendar'));
        }
    }

    public function buscar()
    {
        $this->error = '';

        $telefone = preg_replace('/\D/', '', $this->telefone);
        if (strlen($telefone) < 10) {
            $this->error = 'Digite um telefone válido com DDD.';
            return;
        }

        $this->telefone = $telefone;
        $cliente = Cliente::where('telefone', $telefone)->orWhere('whatsapp_id', $telefone)->first();

        if ($cliente) {
            session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
            $this->redirect(route('site.agendar'));
        } else {
            $this->step = 'register';
        }
    }

    public function cadastrar()
    {
        $this->error = '';
        $this->nome = trim($this->nome);

        if (strlen($this->nome) < 3) {
            $this->error = 'Digite seu nome completo.';
            return;
        }

        $cliente = Cliente::create([
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'whatsapp_id' => $this->telefone,
        ]);

        session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
        $this->redirect(route('site.agendar'));
    }

    public function render()
    {
        return view('livewire.site.login-cliente')
            ->layout('layouts.site');
    }
}
