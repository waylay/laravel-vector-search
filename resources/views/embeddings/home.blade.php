<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vector Embeddings Playground</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-linear-to-br from-cyan-50 via-white to-amber-50 text-slate-900">
<main class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-12 sm:px-6 lg:px-8">
    <header class="rounded-2xl border border-slate-200 bg-white/80 p-8 shadow-sm backdrop-blur">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Laravel + OpenAI + pgvector</p>
        <h1 class="mt-3 text-4xl font-semibold leading-tight">Vector embeddings tutorial workspace</h1>
        <p class="mt-4 max-w-3xl text-slate-600">
            Explore three semantic search examples. Each dataset combines standard field filters with
            <code>whereVectorSimilarTo</code> for meaning-based retrieval.
        </p>
    </header>

    <section class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('search.blog-posts') }}" class="group rounded-2xl border border-cyan-200 bg-cyan-100/50 p-6 transition hover:-translate-y-0.5 hover:bg-cyan-100">
            <h2 class="text-xl font-semibold">Blog Posts</h2>
            <p class="mt-2 text-sm text-slate-700">Search tutorials by topic, audience, and semantic intent.</p>
            <p class="mt-4 text-sm font-medium text-cyan-900 group-hover:underline">Open search</p>
        </a>

        <a href="{{ route('search.product-manuals') }}" class="group rounded-2xl border border-emerald-200 bg-emerald-100/50 p-6 transition hover:-translate-y-0.5 hover:bg-emerald-100">
            <h2 class="text-xl font-semibold">Product Manuals</h2>
            <p class="mt-2 text-sm text-slate-700">Find setup and troubleshooting guidance by semantic similarity.</p>
            <p class="mt-4 text-sm font-medium text-emerald-900 group-hover:underline">Open search</p>
        </a>

        <a href="{{ route('search.support-faqs') }}" class="group rounded-2xl border border-amber-200 bg-amber-100/50 p-6 transition hover:-translate-y-0.5 hover:bg-amber-100">
            <h2 class="text-xl font-semibold">Support FAQs</h2>
            <p class="mt-2 text-sm text-slate-700">Query customer answers with category and priority filters.</p>
            <p class="mt-4 text-sm font-medium text-amber-900 group-hover:underline">Open search</p>
        </a>
    </section>
</main>
</body>
</html>
