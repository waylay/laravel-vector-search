<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support FAQ Semantic Search</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-semibold">Support FAQ semantic search</h1>
        <a href="{{ route('home') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-white">Back home</a>
    </div>

    <form method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-5">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search intent..." class="rounded-lg border border-slate-300 px-3 py-2 lg:col-span-4">
        <button type="submit" class="rounded-lg bg-amber-700 px-4 py-2 font-medium text-white">Search</button>
        <select name="category" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
            @endforeach
        </select>

        <select name="product_line" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All product lines</option>
            @foreach($productLines as $productLine)
                <option value="{{ $productLine }}" @selected($filters['product_line'] === $productLine)>{{ $productLine }}</option>
            @endforeach
        </select>

        <select name="priority" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All priorities</option>
            @foreach($priorities as $priority)
                <option value="{{ $priority }}" @selected($filters['priority'] === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>

        {{-- <label class="flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <input type="checkbox" name="rerank" value="1" @checked($filters['rerank'])>
            Rerank
        </label> --}}

        <div class="flex gap-2">
            <input type="number" step="0.01" min="0" max="1" name="min_similarity" value="{{ $filters['min_similarity'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" title="Similarity threshold">
        </div>
    </form>

    @if($semanticSearch)
        <p class="text-sm text-slate-600">Semantic query from OpenAI agent: <span class="font-medium text-slate-900">{{ $semanticSearch }}</span></p>
    @endif

    <div class="grid gap-4">
        @forelse($faqs as $faq)
            <article class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">{{ $faq->category }} • {{ $faq->product_line }} • {{ ucfirst($faq->priority) }}</p>
                <h2 class="mt-2 text-xl font-semibold">{{ $faq->question }}</h2>
                <p class="mt-3 text-slate-600">{{ $faq->answer }}</p>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">No FAQs matched this filter set.</p>
        @endforelse
    </div>
</main>
</body>
</html>
