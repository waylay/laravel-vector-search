<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Post Semantic Search</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-semibold">Blog post semantic search</h1>
        <a href="{{ route('home') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-white">Back home</a>
    </div>

    <form method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-5">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search intent..." class="rounded-lg border border-slate-300 px-3 py-2 lg:col-span-4">
        <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 font-medium text-white">Search</button>
        <select name="topic" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All topics</option>
            @foreach($topics as $topic)
                <option value="{{ $topic }}" @selected($filters['topic'] === $topic)>{{ $topic }}</option>
            @endforeach
        </select>

        <select name="audience" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All audiences</option>
            @foreach($audiences as $audience)
                <option value="{{ $audience }}" @selected($filters['audience'] === $audience)>{{ $audience }}</option>
            @endforeach
        </select>
{{--
        <label class="flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <input type="checkbox" name="rerank" value="1" @checked($filters['rerank'])>
            Rerank
        </label> --}}

        <div class="flex gap-2 lg:col-span-1">
            <input type="number" step="0.01" min="0" max="1" name="min_similarity" value="{{ $filters['min_similarity'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" title="Similarity threshold">
        </div>
    </form>

    @if($semanticSearch)
        <p class="text-sm text-slate-600">Semantic query from OpenAI agent: <span class="font-medium text-slate-900">{{ $semanticSearch }}</span></p>
    @endif

    <div class="grid gap-4">
        @forelse($blogPosts as $post)
            <article class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">{{ $post->topic }} • {{ $post->audience }}</p>
                <h2 class="mt-2 text-xl font-semibold">{{ $post->title }}</h2>
                <p class="mt-2 text-slate-600">{{ $post->excerpt }}</p>
                <p class="mt-3 text-sm text-slate-500">{{ $post->body }}</p>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">No posts matched this filter set.</p>
        @endforelse
    </div>
</main>
</body>
</html>
