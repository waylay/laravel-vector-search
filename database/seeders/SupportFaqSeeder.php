<?php

namespace Database\Seeders;

use App\Models\SupportFaq;
use Illuminate\Database\Seeder;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class SupportFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['billing', 'authentication', 'integration', 'usage', 'compliance'];
        $productLines = ['core', 'analytics', 'automation', 'security'];
        $priorities = ['low', 'medium', 'high'];

        $faqs = [];

        foreach (range(1, 20) as $index) {
            $category = $categories[$index % count($categories)];
            $productLine = $productLines[$index % count($productLines)];
            $priority = $priorities[$index % count($priorities)];

            $faqs[] = [
                'question' => sprintf('FAQ %d: how do I resolve %s issues in %s?', $index, $category, $productLine),
                'answer' => sprintf(
                    'For case %d, open settings, review the %s checklist, apply the recommended %s configuration, and validate the fix from the diagnostics panel.',
                    $index,
                    $category,
                    $productLine,
                ),
                'category' => $category,
                'product_line' => $productLine,
                'priority' => $priority,
            ];
        }

        $inputs = array_map(function (array $faq): string {
            return implode("\n", [
                $faq['question'],
                $faq['answer'],
                $faq['category'],
                $faq['product_line'],
                $faq['priority'],
            ]);
        }, $faqs);

        $response = Embeddings::for($inputs)
            ->dimensions(1536)
            ->generate(provider: Lab::OpenAI, model: 'text-embedding-3-small');

        foreach ($faqs as $index => $faq) {
            SupportFaq::query()->create([
                ...$faq,
                'embedding' => $response->embeddings[$index],
            ]);
        }
    }
}
