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
        $symptoms = [
            'invoices include seats that were already deprovisioned',
            'single sign-on loops back to the login page intermittently',
            'webhook deliveries show 2xx responses but no downstream updates',
            'saved reports run successfully but export files are empty',
            'policy attestations fail because evidence timestamps are missing',
        ];
        $rootCauses = [
            'a stale cache entry in account metadata delayed plan updates',
            'clock skew between identity provider and application exceeded tolerance',
            'the callback endpoint accepted payloads without required version headers',
            'query filters excluded records after a recent taxonomy rename',
            'retention settings archived evidence before the audit window closed',
        ];
        $resolutions = [
            'force a billing sync, regenerate the draft invoice, and confirm proration rules',
            'reset trust settings, rotate signing certificates, and test with a clean browser profile',
            'replay failed webhooks, enable idempotency keys, and verify event schema mappings',
            'rebuild the report dataset, inspect field-level permissions, and rerun export jobs',
            'restore archived artifacts, extend retention policy, and reissue the compliance bundle',
        ];
        $preventions = [
            'enable monthly entitlement audits with approval checkpoints',
            'monitor SSO error rates and alert on sudden issuer drift',
            'pin integration versions and validate contracts in staging first',
            'review saved filters after each taxonomy or schema change',
            'schedule quarterly evidence retention reviews with security owners',
        ];

        $faqs = [];

        foreach (range(1, 20) as $index) {
            $category = $categories[$index % count($categories)];
            $productLine = $productLines[$index % count($productLines)];
            $priority = $priorities[$index % count($priorities)];
            $symptom = $symptoms[$index % count($symptoms)];
            $rootCause = $rootCauses[$index % count($rootCauses)];
            $resolution = $resolutions[$index % count($resolutions)];
            $prevention = $preventions[$index % count($preventions)];

            $faqs[] = [
                'question' => sprintf('FAQ %d: what should I do when %s issues appear in %s?', $index, $category, $productLine),
                'answer' => sprintf(
                    'When %s, start by collecting the request ID, account ID, and timestamp from the diagnostics panel. In most incidents, %s. The recommended fix is to %s, then validate success by rerunning the affected workflow and confirming no new warnings are emitted for 30 minutes. If the problem persists, escalate with logs, trace identifiers, and a copy of the latest configuration snapshot. To reduce repeat tickets, %s.',
                    $symptom,
                    $rootCause,
                    $resolution,
                    $prevention,
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
