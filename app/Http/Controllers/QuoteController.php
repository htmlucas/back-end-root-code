<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'destino' => 'required|string|max:255|in:nacional,americas,europa',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'viajantes' => 'required|array|min:1',
            'viajantes.*.nome' => 'required|string|max:255',
            'viajantes.*.data_nascimento' => 'required|date',
            'viajantes.*.adicionais' => 'array',
            'viajantes.*.adicionais.*' => 'string|in:bagagem,esportes_aventura'
        ]);

        

        $response = [
            'dias_cobrados' => 0,
            'viajantes' => [],
            'avisos' => [],
            'desconto_grupo_percentual' => 0,
            'total_final' => 0
        ];

        $total = 0;


        foreach($validatedData['viajantes'] as $viajante) {
            
            $diasCobrados = $this->calculateDiasCobrados($validatedData['data_inicio'], $validatedData['data_fim']);
            $tarifa = $this->getTarifaZona($validatedData['destino']);
            $idade = $this->getIdade($viajante['data_nascimento'], $validatedData['data_inicio']);
            $base = $this->basePrice($tarifa, $diasCobrados);

            $multiplicador = $this->getMultiplicador($idade);
            $subtotal = $this->calculateSubTotal($base, $multiplicador, $idade, $diasCobrados, $viajante['adicionais'], $viajante['nome']);
            
            $response['dias_cobrados'] = $diasCobrados;

            $response['viajantes'][] = [
                'nome' => $viajante['nome'],
                'idade' => $idade,
                'subtotal' => round($subtotal['subtotal'], 2, PHP_ROUND_HALF_UP),
                'adicionais_aplicados' => $subtotal['adicionais_aplicados'] ?? []
            ];

            if(isset($subtotal['avisos'])) {
                foreach($subtotal['avisos'] as $aviso) {
                    $response['avisos'][] = $aviso;
                }
            }

            $total += $subtotal['subtotal'];
        }

        $desconto_grupo = $this->descontoGrupo(count($validatedData['viajantes']));

        $response['desconto_grupo_percentual'] = $desconto_grupo * 100;

        $total_final = $total - ($total * $desconto_grupo);
        $response['total_final'] = round($total_final, 2, PHP_ROUND_HALF_UP);

        return response()->json($response, 201);
    }

    private function calculateDiasCobrados($data_inicio, $data_fim)
    {
        $startDate = Carbon::parse($data_inicio);
        $endDate = Carbon::parse($data_fim);
        $days = $startDate->diffInDays($endDate) + 1;

        return max($days, 5);
    }

    private function getIdade($data_nascimento, $data_inicio_viagem): int
    {
        $nascimento = Carbon::parse($data_nascimento);
        $data_inicio = Carbon::parse($data_inicio_viagem);

        return $nascimento->diffInYears($data_inicio);
    }

    private function getTarifaZona(string $destino)
    {
        $tarifas = [
            'nacional' => 10.00,
            'americas' => 16.00,
            'europa' => 22.00,
        ];

        return $tarifas[$destino] ?? 0;
    }

    private function basePrice(int $tarifa, int $diasCobrados): float
    {
        return $tarifa * $diasCobrados;
    }

    private function getMultiplicador($idade)
    {
        return match (true) {
            $idade <= 17 => 0.5,
            $idade <= 64 => 1.0,
            default => 2.0,
        };
    }

    private function calculateSubTotal($base, $multiplicador, $idade, int $diasCobrados,array $adicionais, string $nome = "Viajante")
    {
        $subtotal = $base * $multiplicador;
        $adicionaisAplicados = [];

        if( in_array('esportes_aventura', $adicionais)) {

            if($idade >= 18 && $idade <= 64) {
                $subtotal += $subtotal * 0.25;
                $adicionaisAplicados[] = 'esportes_aventura';
            } else {
                $avisos[] = "ESPORTES_AVENTURA não aplicado para {$nome}: fora da faixa etária perimitida (18-64).";
            }
            
        }

        if ( in_array('bagagem', $adicionais)) {
            $subtotal += 3.00 * $diasCobrados;
            $adicionaisAplicados[] = 'bagagem';
        }

        return ['subtotal' => $subtotal, 'avisos' => $avisos ?? [], 'adicionais_aplicados' => $adicionaisAplicados];
    }

    private function descontoGrupo($quantidadeViajantes)
    {
        return match (true) {
            $quantidadeViajantes >= 5 => 0.10,
            default => 0.0,
        };
    }


}
