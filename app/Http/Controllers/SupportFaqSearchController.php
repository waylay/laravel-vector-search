<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SearchQueryAgent;
use App\Models\SupportFaq;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

class SupportFaqSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:255'],
            'product_line' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],
            'min_similarity' => ['nullable', 'numeric', 'between:0,1'],
        ]);

        $query = SupportFaq::query();

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['product_line'])) {
            $query->where('product_line', $filters['product_line']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        $semanticSearch = null;
        if (! empty($filters['search'])) {
            $semanticSearch = $this->semanticQueryFor($filters['search']);
            $query->whereVectorSimilarTo(
                'embedding',
                $semanticSearch,
                minSimilarity: (float) ($filters['min_similarity'] ?? 0.35)
            );
        } else {
            $query->latest();
        }

        return view('embeddings.support-faqs', [
            'faqs' => $query->limit(15)->get(),
            'categories' => SupportFaq::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
            'productLines' => SupportFaq::query()->select('product_line')->distinct()->orderBy('product_line')->pluck('product_line'),
            'priorities' => SupportFaq::query()->select('priority')->distinct()->orderBy('priority')->pluck('priority'),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'product_line' => $filters['product_line'] ?? '',
                'priority' => $filters['priority'] ?? '',
                'min_similarity' => $filters['min_similarity'] ?? 0.35,
            ],
            'semanticSearch' => $semanticSearch,
        ]);
    }

    private function semanticQueryFor(string $search): string
    {
        try {
            $response = SearchQueryAgent::make()->prompt(
                'Rewrite this for semantic customer support FAQ search: '.$search
            );

            return trim($response->text) !== '' ? trim($response->text) : $search;
        } catch (Throwable) {
            return $search;
        }
    }
}
