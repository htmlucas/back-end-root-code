<?php

namespace App\Repository;

use App\Models\Quote;
use Illuminate\Support\Facades\Log;

class QuoteRepository
{
    public function __construct(private Quote $quote){}


    public function index()
    {
        return $this->quote->all();
    }

    public function save(array $input, array $result): void
    {   
        try { 
            $this->quote->create([
                'destino' => $input['destino'],
                'dias_cobrados' => $result['dias_cobrados'],
                'data_inicio' => $input['data_inicio'],
                'data_fim' => $input['data_fim'],
                'viajantes' => $result['viajantes'],
                'avisos' => $result['avisos'],
                'desconto_grupo_percentual' => $result['desconto_grupo_percentual'],
                'total_final' => $result['total_final'],
            ]);
        } catch (\Exception $e) {
            
           Log::error('Error saving quote:', ['message' => $e->getMessage()]);
        }
        

    }

}