<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SmokeViewsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_paginas_renderizam(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'smoke@test.com'],
            ['name' => 'Smoke Test', 'password' => bcrypt('123456')]
        );

        $barbearia = Barbearia::create([
            'nome' => 'Smoke Test Barbearia',
            'slug' => 'smoke-test-' . uniqid(),
            'owner_id' => $user->id,
        ]);

        $barbeiro = Barbeiro::create([
            'barbearia_id' => $barbearia->id,
            'nome' => 'Barbeiro Teste',
            'email' => 'barbeiro' . uniqid() . '@test.com',
            'password' => bcrypt('123456'),
        ]);

        $cliente = Cliente::create([
            'barbearia_id' => $barbearia->id,
            'nome' => 'Cliente Teste',
            'telefone' => '1199999' . random_int(1000, 9999),
        ]);

        $produto = Produto::create([
            'barbearia_id' => $barbearia->id,
            'nome' => 'Pomada Modeladora',
            'categoria' => 'Cabelo',
            'preco' => 29.9,
            'estoque' => 5,
            'ativo' => true,
        ]);

        $ag = Agendamento::create([
            'barbearia_id' => $barbearia->id,
            'barbeiro_id' => $barbeiro->id,
            'cliente_id' => $cliente->id,
            'data' => now()->format('Y-m-d'),
            'hora_inicio' => '10:00',
            'hora_fim' => '10:30',
            'status' => 'pendente',
            'total' => 50,
        ]);

        $venda = Venda::create([
            'barbearia_id' => $barbearia->id,
            'cliente_id' => $cliente->id,
            'total' => 29.9,
            'status' => 'pendente',
        ]);
        $venda->produtos()->attach($produto->id, ['quantidade' => 1, 'preco_unitario' => 29.9, 'subtotal' => 29.9]);

        $this->actingAs($user);

        $cases = [
            ['admin.dashboard', []],
            ['admin.agendamentos.index', []],
            ['admin.produtos.index', []],
            ['admin.vendas.index', []],
            ['admin.vendas.create', []],
            ['admin.vendas.edit', ['venda' => $venda->id]],
            ['admin.clientes.show', ['cliente' => $cliente->id]],
            ['tenant.admin.dashboard', [$barbearia->slug]],
            ['tenant.admin.agendamentos.index', [$barbearia->slug]],
            ['tenant.admin.produtos.index', [$barbearia->slug]],
            ['tenant.admin.vendas.index', [$barbearia->slug]],
            ['tenant.admin.vendas.create', [$barbearia->slug]],
            ['tenant.admin.vendas.edit', [$barbearia->slug, 'venda' => $venda->id]],
            ['tenant.admin.clientes.show', [$barbearia->slug, 'cliente' => $cliente->id]],
        ];

        foreach ($cases as [$route, $params]) {
            $response = $this->get(route($route, $params));
            if ($response->status() >= 500) {
                fwrite(STDERR, "\n=== $route ===\n" . optional($response->exception)->getMessage() . "\n" . optional($response->exception)->getFile() . ':' . optional($response->exception)->getLine() . "\n");
            }
            $this->assertTrue(
                $response->status() < 500,
                "Rota $route retornou {$response->status()}"
            );
            $response->assertSuccessful();
        }

        $post = $this->post(route('admin.vendas.store'), [
            'cliente_id' => $cliente->id,
            'produto_ids' => [$produto->id],
            'quantidades' => [$produto->id => 2],
            'desconto' => 5,
            'status' => 'pendente',
            'forma_pagamento' => 'pix',
        ]);
        $post->assertRedirect();
        $this->assertDatabaseHas('vendas', ['cliente_id' => $cliente->id, 'status' => 'pendente']);

        $novaVenda = Venda::where('cliente_id', $cliente->id)->where('status', 'pendente')->latest('id')->first();
        $this->assertEquals(54.8, round((float) $novaVenda->total, 2));
        $this->assertEquals(1, $novaVenda->produtos()->count());
        $this->assertEquals(2, $novaVenda->produtos()->first()->pivot->quantidade);

        $this->put(route('admin.vendas.update', $novaVenda), [
            'cliente_id' => $cliente->id,
            'produtos' => [$produto->id => 1],
            'desconto' => 0,
            'status' => 'finalizada',
            'forma_pagamento' => 'dinheiro',
        ])->assertRedirect();

        $novaVenda->refresh();
        $this->assertEquals('finalizada', $novaVenda->status);
        $this->assertNotNull($novaVenda->finalizada_at);

        $this->assertDatabaseHas('caixas', [
            'barbearia_id' => $barbearia->id,
            'data' => now()->format('Y-m-d'),
            'fechado' => false,
        ]);
    }
}