<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Repository\QuoteRepository;
use App\Services\QuoteServices;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    public QuoteServices $quoteService;
    public QuoteRepository $quoteRepository;

    public function __construct(QuoteServices $quoteService, QuoteRepository $quoteRepository)
    {
        $this->quoteService = $quoteService;
        $this->quoteRepository = $quoteRepository;
    }

    public function index()
    {
        $quotes = $this->quoteRepository->index();
        return response()->json($quotes);
    }

    public function store(QuoteRequest $request)
    {
        
        $data = $request->validated();
        $calculate = $this->quoteService->calculate($data);

        $this->quoteRepository->save($data, $calculate);

        return response()->json(
            $this->quoteService->calculate($data),
            201
        );
    }



}
