<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SearchQueryAgent;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

class BlogPostSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:500'],
            'topic' => ['nullable', 'string', 'max:255'],
            'audience' => ['nullable', 'string', 'max:255'],
            'min_similarity' => ['nullable', 'numeric', 'between:0,1'],
        ]);

        $query = BlogPost::query();

        if (! empty($filters['topic'])) {
            $query->where('topic', $filters['topic']);
        }

        if (! empty($filters['audience'])) {
            $query->where('audience', $filters['audience']);
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

        return view('embeddings.blog-posts', [
            'blogPosts' => $query->limit(15)->get(),
            'topics' => BlogPost::query()->select('topic')->distinct()->orderBy('topic')->pluck('topic'),
            'audiences' => BlogPost::query()->select('audience')->distinct()->orderBy('audience')->pluck('audience'),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'topic' => $filters['topic'] ?? '',
                'audience' => $filters['audience'] ?? '',
                'min_similarity' => $filters['min_similarity'] ?? 0.35,
            ],
            'semanticSearch' => $semanticSearch,
        ]);
    }

    private function semanticQueryFor(string $search): string
    {
        return $search;
        try {
            $response = SearchQueryAgent::make()->prompt(
                'Rewrite this for semantic blog search: '.$search
            );

            return trim($response->text) !== '' ? trim($response->text) : $search;
        } catch (Throwable) {
            return $search;
        }
    }
}
