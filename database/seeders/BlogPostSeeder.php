<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = ['Laravel', 'AI', 'Search', 'PostgreSQL', 'DevOps'];
        $audiences = ['beginner', 'intermediate', 'advanced'];
        $formats = ['case study', 'playbook', 'postmortem', 'hands-on guide'];
        $contexts = [
            'a B2B support portal',
            'an internal developer platform',
            'a customer analytics dashboard',
            'a multi-tenant SaaS control plane',
            'an e-commerce recommendation flow',
        ];
        $painPoints = [
            'cold-start results were irrelevant for long-tail queries',
            'search relevance regressed after a schema update',
            'response times spiked under mixed keyword and semantic traffic',
            'support teams could not explain why certain documents ranked first',
            'stale embeddings caused outdated guidance to appear in top results',
        ];
        $techniques = [
            'query rewriting with intent labels',
            'hybrid ranking that blends lexical and vector scores',
            'metadata-aware chunking and selective re-indexing',
            'offline relevance evaluation with golden question sets',
            'progressive rollout with shadow traffic and score diffs',
        ];
        $outcomes = [
            'lifted successful self-serve sessions by 18%',
            'reduced median retrieval latency from 420ms to 190ms',
            'cut false-positive matches by 31% on production queries',
            'improved first-result click-through by 24% in two weeks',
            'lowered support escalation volume during peak releases',
        ];
        $checklists = [
            'instrumenting query classes and fallback intents',
            'testing multilingual prompts and typo-heavy inputs',
            'setting quality gates before re-embedding historical data',
            'documenting acceptance criteria for ranking changes',
            'tracking drift with weekly relevance snapshots',
        ];

        $posts = [];

        foreach (range(1, 20) as $index) {
            $topic = $topics[$index % count($topics)];
            $audience = $audiences[$index % count($audiences)];
            $format = $formats[$index % count($formats)];
            $context = $contexts[$index % count($contexts)];
            $painPoint = $painPoints[$index % count($painPoints)];
            $technique = $techniques[$index % count($techniques)];
            $outcome = $outcomes[$index % count($outcomes)];
            $checklist = $checklists[$index % count($checklists)];

            $posts[] = [
                'title' => sprintf('%s %s %d: improving retrieval quality in production', $topic, $format, $index),
                'topic' => $topic,
                'audience' => $audience,
                'excerpt' => sprintf(
                    'In installment %d, we break down how teams using %s solved relevance issues in %s and built a repeatable rollout plan for %s readers.',
                    $index,
                    $topic,
                    $context,
                    $audience,
                ),
                'body' => sprintf(
                    'The team started by mapping the top 200 user questions and discovered that %s. We then implemented %s, paired it with a stricter ingest contract, and compared ranking deltas against a curated benchmark set. The article walks through schema decisions, failure modes, and trade-offs that appeared after the first deployment window. You will also get a practical checklist for %s, along with examples of prompt templates and metadata filters that held up under real traffic. By the end, the organization %s while keeping operational complexity manageable for %s teams.',
                    $painPoint,
                    $technique,
                    $checklist,
                    $outcome,
                    $audience,
                ),
            ];
        }

        $inputs = array_map(function (array $post): string {
            return implode("\n", [
                $post['title'],
                $post['topic'],
                $post['audience'],
                $post['excerpt'],
                $post['body'],
            ]);
        }, $posts);

        $response = Embeddings::for($inputs)
            ->dimensions(1536)
            ->generate(provider: Lab::OpenAI, model: 'text-embedding-3-small');

        foreach ($posts as $index => $post) {
            BlogPost::query()->create([
                ...$post,
                'embedding' => $response->embeddings[$index],
            ]);
        }
    }
}
