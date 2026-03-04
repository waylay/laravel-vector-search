<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SearchQueryAgent;
use App\Models\ProductManual;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

class ProductManualSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:500'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'max:255'],
            'min_similarity' => ['nullable', 'numeric', 'between:0,1'],
            'rerank' => ['nullable', 'boolean'],
        ]);

        $query = ProductManual::query();

        if (! empty($filters['product_name'])) {
            $query->where('product_name', $filters['product_name']);
        }

        if (! empty($filters['section'])) {
            $query->where('section', $filters['section']);
        }

        if (! empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        $semanticSearch = null;
        if (! empty($filters['search'])) {
            // $semanticSearch = $this->semanticQueryFor($filters['search']);
            $query->whereVectorSimilarTo(
                'embedding',
                // $semanticSearch,
                'product manuals about ' . $filters['search'],
                minSimilarity: (float) ($filters['min_similarity'] ?? 0.35)
            );
        } else {
            $query->latest();
        }

        $manuals = $query->limit(15)->get();

        if (! empty($filters['search']) && (bool) ($filters['rerank'] ?? false)) {
            try {
                $manuals = $manuals->rerank(
                    by: ['product_name', 'section', 'content'],
                    query: $semanticSearch ?? $filters['search'],
                    limit: 15,
                );
            } catch (Throwable) {
            }
        }

        return view('embeddings.product-manuals', [
            'manuals' => $manuals,
            'productNames' => ProductManual::query()->select('product_name')->distinct()->orderBy('product_name')->pluck('product_name'),
            'sections' => ProductManual::query()->select('section')->distinct()->orderBy('section')->pluck('section'),
            'difficulties' => ProductManual::query()->select('difficulty')->distinct()->orderBy('difficulty')->pluck('difficulty'),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'product_name' => $filters['product_name'] ?? '',
                'section' => $filters['section'] ?? '',
                'difficulty' => $filters['difficulty'] ?? '',
                'min_similarity' => $filters['min_similarity'] ?? 0.35,
                'rerank' => (bool) ($filters['rerank'] ?? false),
            ],
            'semanticSearch' => $semanticSearch,
        ]);
    }

    private function semanticQueryFor(string $search): string
    {
        try {
            $response = SearchQueryAgent::make()->prompt(
                'Rewrite this for semantic product manual search: '.$search
            );

            return trim($response->text) !== '' ? trim($response->text) : $search;
        } catch (Throwable) {
            return $search;
        }
    }
}
