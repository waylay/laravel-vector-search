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
        $platforms = ['Ubuntu 24.04', 'Debian 12', 'RHEL 9', 'Kubernetes', 'Docker Compose'];
        $prerequisites = [
            'verify outbound HTTPS access to package mirrors and model providers',
            'confirm service accounts have least-privilege database credentials',
            'reserve dedicated CPU for indexing workers during initial bootstrap',
            'synchronize server time with NTP to avoid token expiration drift',
            'snapshot existing configuration before applying migration scripts',
        ];
        $validationSteps = [
            'run the health-check endpoint and ensure all dependencies report green',
            'execute a sample search suite and compare scores to the baseline file',
            'inspect queue depth and worker retry counts for 15 minutes',
            'verify audit logs include actor, action, and correlation identifiers',
            'perform failover simulation and measure recovery within SLO targets',
        ];
        $failurePatterns = [
            'index build stalls when document chunk size exceeds memory limits',
            'API calls degrade after connection pooling is disabled accidentally',
            'partial deploys produce schema drift between worker and API nodes',
            'cache eviction bursts can hide newly published content temporarily',
            'rotated secrets fail if sidecars are not reloaded in sequence',
        ];

        $manuals = [];

        foreach (range(1, 20) as $index) {
            $product = $products[$index % count($products)];
            $version = $versions[$index % count($versions)];
            $section = $sections[$index % count($sections)];
            $difficulty = $difficulties[$index % count($difficulties)];
            $platform = $platforms[$index % count($platforms)];
            $prerequisite = $prerequisites[$index % count($prerequisites)];
            $validationStep = $validationSteps[$index % count($validationSteps)];
            $failurePattern = $failurePatterns[$index % count($failurePatterns)];

            $manuals[] = [
                'product_name' => $product,
                'version' => $version,
                'section' => $section,
                'difficulty' => $difficulty,
                'content' => sprintf(
                    'Section %d for %s %s covers %s procedures on %s environments. Start by completing the prerequisite checklist: %s. The main workflow includes exact command ordering, expected runtime behavior, and rollback notes for unexpected state transitions. During validation, %s and record the resulting metrics in the release worksheet so teams can compare pre and post deployment quality. A frequent issue in this stage is that %s, and this manual provides a decision tree with targeted remediation for %s operators.',
                    $index,
                    $product,
                    $version,
                    $section,
                    $platform,
                    $prerequisite,
                    $validationStep,
                    $failurePattern,
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
