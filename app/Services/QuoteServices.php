<?php

namespace App\Services;

use App\Repository\QuoteRepository;
use Carbon\Carbon;

class QuoteServices
{
    public function __construct(private QuoteRepository $quoteRepository){}

    public function calculate(array $data): array
    {
        $response = [
            'dias_cobrados' => 0,
            'viajantes' => [],
            'avisos' => [],
            'desconto_grupo_percentual' => 0,
            'total_final' => 0
        ];

        $total = 0;


        foreach($data['viajantes'] as $viajante) {
            
            $diasCobrados = $this->calculateDiasCobrados($data['data_inicio'], $data['data_fim']);
            $tarifa = $this->getTarifaZona($data['destino']);
            $idade = $this->getIdade($viajante['data_nascimento'], $data['data_inicio']);
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

        $desconto_grupo = $this->descontoGrupo(count($data['viajantes']));

        $response['desconto_grupo_percentual'] = $desconto_grupo * 100;

        $total_final = $total - ($total * $desconto_grupo);
        $response['total_final'] = round($total_final, 2, PHP_ROUND_HALF_UP);

        return $response;
    }

    private function calculateDiasCobrados($data_inicio, $data_fim): int
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

        return $nascimento->diff($data_inicio)->y;
    }

    private function getTarifaZona(string $destino): float
    {
        $tarifas = [
            'nacional' => 10.00,
            'americas' => 16.00,
            'europa' => 22.00,
        ];

        return $tarifas[$destino] ?? 0;
    }

    private function basePrice(float $tarifa, int $diasCobrados): float
    {
        return $tarifa * $diasCobrados;
    }

    private function getMultiplicador($idade): float
    {
        return match (true) {
            $idade <= 17 => 0.5,
            $idade <= 64 => 1.0,
            default => 2.0,
        };
    }

    private function calculateSubTotal($base, $multiplicador, $idade, int $diasCobrados,array $adicionais, string $nome = "Viajante"): array
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

    private function descontoGrupo($quantidadeViajantes): float
    {
        return match (true) {
            $quantidadeViajantes >= 5 => 0.10,
            default => 0.0,
        };
    }

}