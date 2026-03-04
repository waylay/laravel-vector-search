<?php

namespace Database\Seeders;

use App\Models\ProductManual;
use Illuminate\Database\Seeder;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class ProductManualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = ['VectorPad', 'SignalDesk', 'QueryLens', 'OpsBeacon', 'DataHarbor'];
        $versions = ['v1.0', 'v1.1', 'v2.0', 'v2.1'];
        $sections = ['installation', 'configuration', 'operations', 'troubleshooting'];
        $difficulties = ['basic', 'standard', 'expert'];

        $manuals = [];

        foreach (range(1, 20) as $index) {
            $product = $products[$index % count($products)];
            $version = $versions[$index % count($versions)];
            $section = $sections[$index % count($sections)];
            $difficulty = $difficulties[$index % count($difficulties)];

            $manuals[] = [
                'product_name' => $product,
                'version' => $version,
                'section' => $section,
                'difficulty' => $difficulty,
                'content' => sprintf(
                    'Manual step %d for %s %s focuses on %s workflows. It documents command examples, configuration defaults, common errors, and remediation guidance tailored for %s users.',
                    $index,
                    $product,
                    $version,
                    $section,
                    $difficulty,
                ),
            ];
        }

        $inputs = array_map(function (array $manual): string {
            return implode("\n", [
                $manual['product_name'],
                $manual['version'],
                $manual['section'],
                $manual['difficulty'],
                $manual['content'],
            ]);
        }, $manuals);

        $response = Embeddings::for($inputs)
            ->dimensions(1536)
            ->generate(provider: Lab::OpenAI, model: 'text-embedding-3-small');

        foreach ($manuals as $index => $manual) {
            ProductManual::query()->create([
                ...$manual,
                'embedding' => $response->embeddings[$index],
            ]);
        }
    }
}
