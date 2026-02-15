<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketController extends ApiController
{
    /**
     * List markets with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Market::query();

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Search by name or symbol
            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('symbol', 'like', '%' . $request->search . '%');
                });
            }

            // Sorting
            if ($request->has('sort')) {
                $direction = $request->has('direction') && $request->direction === 'asc' ? 'asc' : 'desc';
                $query->orderBy($request->sort, $direction);
            } else {
                $query->latest();
            }

            $markets = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($markets, 'Markets retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve markets: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get market details by symbol
     */
    public function show($symbol): JsonResponse
    {
        try {
            $market = Market::where('symbol', strtoupper($symbol))->first();

            if (!$market) {
                return $this->notFound('Market not found');
            }

            return $this->success(['market' => $market], 'Market retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve market: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get available categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Market::distinct()
                ->pluck('category')
                ->sort()
                ->values();

            return $this->success(['categories' => $categories], 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve categories: ' . $e->getMessage(), 500);
        }
    }
}
