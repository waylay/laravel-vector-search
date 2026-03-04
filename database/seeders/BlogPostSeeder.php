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

        $posts = [];

        foreach (range(1, 20) as $index) {
            $topic = $topics[$index % count($topics)];
            $audience = $audiences[$index % count($audiences)];

            $posts[] = [
                'title' => sprintf('%s tutorial %d: practical vector search patterns', $topic, $index),
                'topic' => $topic,
                'audience' => $audience,
                'excerpt' => sprintf('Lesson %d explains how %s teams build more relevant semantic retrieval.', $index, strtolower($topic)),
                'body' => sprintf(
                    'This article demonstrates query intent handling, metadata filters, and whereVectorSimilarTo tuning for %s use cases. It includes quality checks, batching guidance, and relevance threshold examples for %s readers.',
                    strtolower($topic),
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
