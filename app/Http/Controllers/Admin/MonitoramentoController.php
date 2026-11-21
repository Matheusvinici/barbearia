<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Agendamento;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoramentoController extends Controller
{
    use TenantScoped;

    private const STATUS_VISITA = ['realizado', 'confirmado'];

    public static function mensagemPadrao(?string $barbeariaNome = null): string
    {
        $unidade = $barbeariaNome ? " aqui da {$barbeariaNome}" : '';
        return "Olá {nome}! Sentimos sua falta{$unidade} ✂️ Manter os cuidados com a barba e o cabelo são essenciais para o visual masculino. Que tal agendar seu próximo horário? Estamos te esperando! 💈";
    }

    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return '';
        }
        // Prefixa Brasil quando vier sem DDI (10 ou 11 dígitos)
        if (!str_starts_with($digits, '55') && (strlen($digits) === 10 || strlen($digits) === 11)) {
            $digits = '55' . $digits;
        }
        return $digits;
    }

    public static function waLink(string $phoneDigits, string $mensagem): string
    {
        return 'https://wa.me/' . $phoneDigits . '?text=' . rawurlencode($mensagem);
    }

    private function buildDataset(Request $request): array
    {
        $tipo = $request->get('tipo', 'todos');
        $diasAtraso = max(1, (int) $request->get('dias_atraso', 30));
        $diasUmaVez = max(1, (int) $request->get('dias_uma_vez', 30));
        $busca = trim((string) $request->get('q', ''));
        $tenant = $this->getTenant();
        $barbeariaNome = $tenant?->nome ?? config('app.name', 'Barbearia');

        $template = (string) ($request->get('mensagem', '') ?: self::mensagemPadrao($barbeariaNome));

        // --- Clientes do escopo (tenant estrito) ---
        $clientesQuery = Cliente::query();
        $clientesQuery = $this->applyTenantScope($clientesQuery);
        if ($busca !== '') {
            $clientesQuery->where(function ($q) use ($busca) {
                $q->where('nome', 'LIKE', "%{$busca}%")
                    ->orWhere('telefone', 'LIKE', "%{$busca}%");
            });
        }
        $clientes = $clientesQuery->orderBy('nome')->get();

        $clienteIds = $clientes->pluck('id')->all();

        // --- Agendamentos considerados visita ---
        $agQuery = Agendamento::with('barbeiro')
            ->whereIn('cliente_id', $clienteIds ?: [0])
            ->whereIn('status', self::STATUS_VISITA);
        if ($this->isTenantContext()) {
            $agQuery = $this->applyTenantScope($agQuery);
        }
        $agendamentos = $agQuery->orderBy('data')->get()->groupBy('cliente_id');

        $hoje = Carbon::today();
        $rows = [];

        foreach ($clientes as $cliente) {
            $visitas = ($agendamentos->get($cliente->id) ?? collect())->sortBy('data')->values();
            $total = $visitas->count();
            $datas = $visitas->map(fn ($a) => $a->data instanceof Carbon ? $a->data->copy()->startOfDay() : Carbon::parse($a->data)->startOfDay());

            $primeira = $datas->first();
            $ultima = $datas->last();
            $diasDesdeUltima = $ultima ? (int) $ultima->diffInDays($hoje) : null;

            $mediaIntervalo = null;
            if ($total > 1 && $primeira && $ultima) {
                $span = (int) $primeira->diffInDays($ultima);
                $mediaIntervalo = $total > 1 ? round($span / ($total - 1), 1) : null;
            }

            // Queda de frequência: dias desde última > 1.8x média (3+ visitas)
            // ou segunda metade mais espaçada que a primeira (4+ visitas, razão >= 1.5)
            $queda = false;
            $quedaMotivo = null;
            $razao = null;
            if ($total >= 3 && $mediaIntervalo && $mediaIntervalo > 0 && $diasDesdeUltima !== null && $diasDesdeUltima > ($mediaIntervalo * 1.8)) {
                $queda = true;
                $quedaMotivo = "sem voltar há {$diasDesdeUltima}d (média era {$mediaIntervalo}d)";
            }
            if ($total >= 4) {
                $sorted = $datas->values();
                $mid = (int) floor($total / 2);
                $primeiraMetade = $sorted->slice(0, $mid + 1);
                $segundaMetade = $sorted->slice($mid);
                $avg = function ($col) {
                    if ($col->count() < 2) return null;
                    $span = (int) $col->first()->diffInDays($col->last());
                    return $span / ($col->count() - 1);
                };
                $avgAntigo = $avg($primeiraMetade);
                $avgRecente = $avg($segundaMetade);
                if ($avgAntigo && $avgAntigo > 0 && $avgRecente !== null) {
                    $razao = round($avgRecente / $avgAntigo, 2);
                    if ($razao >= 1.5 && !$queda) {
                        $queda = true;
                        $quedaMotivo = "ritmo caiu {$razao}x (antes ~{$avgAntigo}d, agora ~{$avgRecente}d)";
                    }
                }
            }

            $ultimoBarbeiro = $visitas->last()?->barbeiro?->nome;

            $digits = self::normalizePhone($cliente->telefone);
            $primeiroNome = trim(explode(' ', (string) $cliente->nome)[0] ?? (string) $cliente->nome);
            $mensagem = str_replace(['{nome}', '{barbearia}'], [$primeiroNome, $barbeariaNome], $template);

            $rows[] = [
                'cliente' => $cliente,
                'total' => $total,
                'primeira' => $primeira,
                'ultima' => $ultima,
                'dias' => $diasDesdeUltima,
                'media' => $mediaIntervalo,
                'queda' => $queda,
                'queda_motivo' => $quedaMotivo,
                'razao' => $razao,
                'ultimo_barbeiro' => $ultimoBarbeiro,
                'digits' => $digits,
                'mensagem' => $mensagem,
            ];
        }

        // Duplicados por telefone (cruzamento nome x telefone)
        $porTelefone = [];
        foreach ($rows as $r) {
            if ($r['digits'] !== '') {
                $porTelefone[$r['digits']][] = $r['cliente']->id;
            }
        }
        foreach ($rows as &$r) {
            $r['duplicado'] = $r['digits'] !== '' && count($porTelefone[$r['digits']] ?? []) > 1;
        }
        unset($r);

        $comVisita = array_filter($rows, fn ($r) => $r['total'] > 0);
        $semVisita = array_filter($rows, fn ($r) => $r['total'] === 0);

        $atrasados = array_values(array_filter($comVisita, fn ($r) => $r['dias'] !== null && $r['dias'] >= $diasAtraso));
        usort($atrasados, fn ($a, $b) => ($b['dias'] ?? 0) <=> ($a['dias'] ?? 0));

        $umaVez = array_values(array_filter($comVisita, fn ($r) => $r['total'] === 1 && $r['dias'] !== null && $r['dias'] >= $diasUmaVez));
        usort($umaVez, fn ($a, $b) => ($b['dias'] ?? 0) <=> ($a['dias'] ?? 0));

        $quedaList = array_values(array_filter($comVisita, fn ($r) => $r['queda']));
        usort($quedaList, function ($a, $b) {
            $ra = $a['razao'] ?? ($a['dias'] && $a['media'] ? $a['dias'] / max($a['media'], 0.1) : 0);
            $rb = $b['razao'] ?? ($b['dias'] && $b['media'] ? $b['dias'] / max($b['media'], 0.1) : 0);
            return $rb <=> $ra;
        });

        return [
            'tipo' => in_array($tipo, ['todos', 'atrasados', 'uma_vez', 'queda']) ? $tipo : 'todos',
            'diasAtraso' => $diasAtraso,
            'diasUmaVez' => $diasUmaVez,
            'busca' => $busca,
            'template' => $template,
            'barbeariaNome' => $barbeariaNome,
            'tenantSlug' => $tenant?->slug,
            'atrasados' => $atrasados,
            'umaVez' => $umaVez,
            'queda' => $quedaList,
            'semVisitaCount' => count($semVisita),
            'totalClientes' => count($rows),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->buildDataset($request);
        return view('admin.monitoramento.index', $data);
    }

    public function export(Request $request): StreamedResponse
    {
        $data = $this->buildDataset($request);
        $tipo = $request->get('export_tipo', $data['tipo']);
        $map = [
            'atrasados' => $data['atrasados'],
            'uma_vez' => $data['umaVez'],
            'queda' => $data['queda'],
        ];
        $list = $map[$tipo] ?? array_merge($data['atrasados'], $data['umaVez'], $data['queda']);
        // remove duplicados por cliente id
        $seen = [];
        $list = array_values(array_filter($list, function ($r) use (&$seen) {
            $id = $r['cliente']->id;
            if (isset($seen[$id])) return false;
            $seen[$id] = true;
            return true;
        }));

        $filename = 'monitoramento_' . $tipo . '_' . now()->format('Ymd_His') . '.csv';
        return response()->streamDownload(function () use ($list) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['nome', 'telefone', 'telefone_digits', 'visitas', 'ultima_visita', 'dias_sem_voltar', 'media_dias', 'queda_motivo', 'ultimo_barbeiro', 'wa_link'], ';');
            foreach ($list as $r) {
                fputcsv($out, [
                    $r['cliente']->nome,
                    $r['cliente']->telefone,
                    $r['digits'],
                    $r['total'],
                    $r['ultima'] ? $r['ultima']->format('Y-m-d') : '',
                    $r['dias'] ?? '',
                    $r['media'] ?? '',
                    $r['queda_motivo'] ?? '',
                    $r['ultimo_barbeiro'] ?? '',
                    $r['digits'] ? self::waLink($r['digits'], $r['mensagem']) : '',
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
