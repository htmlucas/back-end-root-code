<?php

namespace Tests\Unit;

use App\Services\QuoteServices;
use PHPUnit\Framework\TestCase;

class QuoteServiceTest extends TestCase
{

    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function teste_periodo_minimo_deve_ser_5_dias()
    {
        $request = [
            'destino' => 'nacional',
            'data_inicio' => '2026-06-09',
            'data_fim' => '2026-06-09',
            'viajantes' => [
                [
                    'nome' => 'Lucas',
                    'data_nascimento' => '1998-01-27',
                    'adicionais' => []
                ]
            ]
        ];

        $service = new QuoteServices();

        $calculate = $service->calculate($request);

        $this->assertEquals(5, $calculate['dias_cobrados']);
    }

    public function teste_calculo_idade()
    {
        $request = [
            'destino' => 'nacional',
            'data_inicio' => '2026-06-09',
            'data_fim' => '2026-06-14',
            'viajantes' => [
                [
                    'nome' => 'Lucas',
                    'data_nascimento' => '1998-01-27',
                    'adicionais' => []
                ]
            ]
        ];

        $service = new QuoteServices();

        $calculate = $service->calculate($request);

        $this->assertEquals(28, $calculate['viajantes'][0]['idade']);
    }

    public function teste_esportes_aventura_aplicado()
    {
        $request = [
            'destino' => 'nacional',
            'data_inicio' => '2026-06-09',
            'data_fim' => '2026-06-14',
            'viajantes' => [
                [
                    'nome' => 'Lucas Idoso',
                    'data_nascimento' => '1950-01-27',
                    'adicionais' => ['esportes_aventura']
                ]
            ]
        ];

        $service = new QuoteServices();

        $calculate = $service->calculate($request);

        $this->assertContains('ESPORTES_AVENTURA não aplicado para Lucas Idoso: fora da faixa etária perimitida (18-64).', $calculate['avisos']);
    }

    public function teste_desconto_grupo_aplicado()
    {
        $request = [
            'destino' => 'nacional',
            'data_inicio' => '2026-06-09',
            'data_fim' => '2026-06-14',
            'viajantes' => [
                [
                    'nome' => 'Viajante 1',
                    'data_nascimento' => '1990-01-27',
                    'adicionais' => []
                ],
                [
                    'nome' => 'Viajante 2',
                    'data_nascimento' => '1990-01-27',
                    'adicionais' => []
                ],
                [
                    'nome' => 'Viajante 3',
                    'data_nascimento' => '1990-01-27',
                    'adicionais' => []
                ],
                [
                    'nome' => 'Viajante 4',
                    'data_nascimento' => '1990-01-27',
                    'adicionais' => []
                ],
                [
                    'nome' => 'Viajante 5',
                    'data_nascimento' => '1990-01-27',
                    'adicionais' => []
                ]
            ]
        ];

        $service = new QuoteServices();

        $calculate = $service->calculate($request);

        $this->assertEquals(10, $calculate['desconto_grupo_percentual']);
    }

    public function test_deve_calcular_cotacao_completa_com_multiplos_viajantes_e_adicionais()
    {
        $request = [
            'destino' => 'nacional',
            'data_inicio' => '2026-07-10',
            'data_fim' => '2026-07-14',
            'viajantes' => [
                [
                    'nome' => 'Viajante 1',
                    'data_nascimento' => '1995-01-01',
                    'adicionais' => ['esportes_aventura', 'bagagem']
                ],
                [
                    'nome' => 'Viajante 2',
                    'data_nascimento' => '1990-02-02',
                    'adicionais' => ['bagagem']
                ],
                [
                    'nome' => 'Viajante 3',
                    'data_nascimento' => '1945-03-03',
                    'adicionais' => ['bagagem','esportes_aventura']
                ],
                [
                    'nome' => 'Viajante 4',
                    'data_nascimento' => '1992-04-04',
                    'adicionais' => ['bagagem','esportes_aventura']
                ],
                [
                    'nome' => 'Viajante 5',
                    'data_nascimento' => '1998-05-05',
                    'adicionais' => ['esportes_aventura']
                ]
            ]
        ];

        $service = new QuoteServices();

        $calculate = $service->calculate($request);

        $this->assertEquals(5, $calculate['dias_cobrados']);
        $this->assertEquals(77.50, $calculate['viajantes'][0]['subtotal']);
        $this->assertEquals(65, $calculate['viajantes'][1]['subtotal']);
        $this->assertEquals(115, $calculate['viajantes'][2]['subtotal']);
        $this->assertContains(
            'bagagem',
            $calculate['viajantes'][2]['adicionais_aplicados']
        );
        $this->assertEquals(77.50, $calculate['viajantes'][3]['subtotal']);
        $this->assertEquals(62.50, $calculate['viajantes'][4]['subtotal']);
        $this->assertEquals("ESPORTES_AVENTURA não aplicado para Viajante 3: fora da faixa etária perimitida (18-64).", $calculate['avisos'][0]);
        $this->assertEquals(10, $calculate['desconto_grupo_percentual']);
        $this->assertEquals(357.75, $calculate['total_final']);
    }
}
