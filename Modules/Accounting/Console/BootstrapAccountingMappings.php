<?php

namespace Modules\Accounting\Console;

use App\BusinessLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\AccountingAccount;
use Modules\Accounting\Entities\AccountingMapping;
use Modules\Accounting\Entities\AccountingMappingKey;
use Modules\Accounting\Utils\MappingResolver;

class BootstrapAccountingMappings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:bootstrap-mappings \
        {--business_id= : Business id} \
        {--location_id= : Location id (required; also used as source when --global is set)} \
        {--apply : Persist mappings (default is dry-run)} \
        {--global : Write mappings at location_id=0 (source still required via --location_id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap accounting mappings from legacy configuration';

    public function handle(): int
    {
        $businessId = (int) $this->option('business_id');
        $sourceLocationId = (int) $this->option('location_id');
        $apply = (bool) $this->option('apply');
        $targetGlobal = (bool) $this->option('global');
        $dryRun = ! $apply;

        if (empty($businessId) || empty($sourceLocationId)) {
            $this->error('business_id and location_id are required');

            return 1;
        }

        $location = BusinessLocation::where('business_id', $businessId)
            ->where('id', $sourceLocationId)
            ->first();

        if (empty($location)) {
            $this->error('Business location not found for the provided business_id and location_id');

            return 1;
        }

        $targetLocationId = $targetGlobal ? 0 : $sourceLocationId;

        [$paymentImports, $paymentIssues] = $this->collectPaymentImports($businessId, $location);
        [$operationImports, $operationIssues] = $this->collectOperationImports($businessId, $location);

        $imports = array_merge($paymentImports, $operationImports);
        $issues = array_merge($paymentIssues, $operationIssues);

        [$imports, $duplicates] = $this->dedupeImports($imports);
        foreach ($duplicates as $dupKey) {
            $this->warn("Duplicate mapping key detected; keeping first occurrence: {$dupKey}");
        }

        if (empty($imports)) {
            $this->warn('No mappings detected from legacy configuration.');
        }

        foreach ($issues as $issue) {
            $this->warn($issue);
        }

        $this->line('Planned mappings:');
        foreach ($imports as $row) {
            $this->line(" - {$row['key']} => account {$row['account_id']} ({$row['source']})");
        }

        $summary = [
            'planned' => count($imports),
            'imported' => count($imports),
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
            'issues' => count($issues) + count($duplicates),
        ];

        if ($dryRun) {
            $this->comment('Dry-run complete. Use --apply to persist mappings.');
            $this->printSummary($summary);

            return 0;
        }

        if (! $this->confirm("Apply mappings for business {$businessId} to location {$targetLocationId}?")) {
            $this->warn('Aborted by user.');
            $this->printSummary($summary);

            return 1;
        }

        $this->applyMappings($businessId, $targetLocationId, $imports, $summary);

        $this->info('Mappings persisted successfully.');
        $this->printSummary($summary);

        return 0;
    }

    private function collectPaymentImports(int $businessId, BusinessLocation $location): array
    {
        $config = json_decode($location->default_payment_accounts ?? '', true);
        if (! is_array($config)) {
            return [[], ['default_payment_accounts is empty or invalid JSON']];
        }

        $imports = [];
        $issues = [];

        foreach (MappingResolver::PAYMENT_KEY_MAP as $mappingKey => $legacyKey) {
            $payload = Arr::get($config, $legacyKey, []);
            $accountId = $payload['account'] ?? null;

            if (empty($payload) || empty($payload['is_enabled']) || empty($accountId)) {
                continue;
            }

            if (! $this->isAccountActive($businessId, $accountId)) {
                $issues[] = "Payment mapping {$mappingKey} references inactive/missing account ({$accountId})";
                continue;
            }

            $imports[] = [
                'key' => $mappingKey,
                'account_id' => (int) $accountId,
                'source' => 'default_payment_accounts',
            ];
        }

        return [$imports, $issues];
    }

    private function collectOperationImports(int $businessId, BusinessLocation $location): array
    {
        $config = json_decode($location->accounting_default_map ?? '', true);
        if (! is_array($config)) {
            return [[], ['accounting_default_map is empty or invalid JSON']];
        }

        $imports = [];
        $issues = [];

        foreach ($config as $operation => $payload) {
            if (! is_array($payload)) {
                continue;
            }

            foreach (['payment_account', 'deposit_to'] as $field) {
                $accountId = $payload[$field] ?? null;

                if (empty($accountId)) {
                    continue;
                }

                if (! $this->isAccountActive($businessId, $accountId)) {
                    $issues[] = sprintf(
                        'Operation mapping %s.%s references inactive/missing account (%s)',
                        $operation,
                        $field,
                        $accountId
                    );
                    continue;
                }

                $key = MappingResolver::formatLegacyKey($operation, $field);
                $imports[] = [
                    'key' => $key,
                    'account_id' => (int) $accountId,
                    'source' => 'accounting_default_map',
                ];
            }
        }

        return [$imports, $issues];
    }

    private function isAccountActive(int $businessId, $accountId): bool
    {
        if (empty($accountId)) {
            return false;
        }

        return AccountingAccount::where('business_id', $businessId)
            ->where('status', 'active')
            ->where('id', $accountId)
            ->exists();
    }

    private function dedupeImports(array $imports): array
    {
        $unique = [];
        $duplicates = [];

        foreach ($imports as $row) {
            $key = $row['key'];
            if (array_key_exists($key, $unique)) {
                $duplicates[] = $key;
                continue;
            }
            $unique[$key] = $row;
        }

        return [array_values($unique), array_values(array_unique($duplicates))];
    }

    private function applyMappings(int $businessId, int $locationId, array $imports, array &$summary): void
    {
        DB::transaction(function () use ($businessId, $locationId, $imports, &$summary) {
            foreach ($imports as $row) {
                $mappingKey = AccountingMappingKey::firstOrCreate(['key' => $row['key']]);

                $existing = AccountingMapping::where([
                    'business_id' => $businessId,
                    'location_id' => $locationId,
                    'key' => $row['key'],
                ])->first();

                if ($existing) {
                    if ((int) $existing->account_id === (int) $row['account_id']) {
                        $summary['skipped']++;
                        continue;
                    }

                    $existing->update([
                        'account_id' => $row['account_id'],
                        'mapping_key_id' => $mappingKey->id,
                    ]);
                    $summary['updated']++;
                    continue;
                }

                AccountingMapping::create([
                    'business_id' => $businessId,
                    'location_id' => $locationId,
                    'key' => $row['key'],
                    'account_id' => $row['account_id'],
                    'mapping_key_id' => $mappingKey->id,
                ]);
                $summary['inserted']++;
            }
        });
    }

    private function printSummary(array $summary): void
    {
        $this->line('Summary:');
        $this->line(" planned: {$summary['planned']}");
        $this->line(" imported: {$summary['imported']}");
        $this->line(" inserted: {$summary['inserted']}");
        $this->line(" updated: {$summary['updated']}");
        $this->line(" skipped: {$summary['skipped']}");
        $this->line(" issues: {$summary['issues']}");
    }
}
