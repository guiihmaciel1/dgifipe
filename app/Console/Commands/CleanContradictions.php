<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanContradictions extends Command
{
    protected $signature = 'listings:clean-contradictions {--dry-run : Show count without deleting}';
    protected $description = 'Remove listings whose title contradicts the stored model or storage';

    public function handle(): int
    {
        $models = array_keys(config('dgifipe.models', []));
        usort($models, fn ($a, $b) => strlen($b) - strlen($a));

        $modelDetect = $this->buildModelDetector($models);
        $storageDetect = $this->buildStorageDetector();

        $contradictionWhere = "
            title != ''
            AND LOWER(title) LIKE '%iphone%'
            AND NOT (
                (({$modelDetect}) IS NULL OR ({$modelDetect}) = LOWER(model))
                AND
                (({$storageDetect}) IS NULL OR ({$storageDetect}) = LOWER(storage))
            )
        ";

        $count = DB::table('market_listings')->whereRaw($contradictionWhere)->count();

        if ($this->option('dry-run')) {
            $this->info("Found {$count} contradictory listings (dry-run, nothing deleted).");
            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('No contradictory listings found.');
            return self::SUCCESS;
        }

        $deleted = DB::table('market_listings')->whereRaw($contradictionWhere)->delete();
        $this->info("Deleted {$deleted} contradictory listings.");

        return self::SUCCESS;
    }

    private function buildModelDetector(array $models): string
    {
        $whens = [];
        foreach ($models as $m) {
            $lower = strtolower($m);
            $pattern = str_replace(' ', ' ?', preg_quote($lower, '/'));
            $escaped = addslashes($pattern);
            $val = addslashes($lower);
            $whens[] = "WHEN LOWER(title) REGEXP '{$escaped}' THEN '{$val}'";
        }

        return 'CASE ' . implode(' ', $whens) . ' ELSE NULL END';
    }

    private function buildStorageDetector(): string
    {
        $storages = ['1tb', '512gb', '256gb', '128gb', '64gb'];
        $whens = [];
        foreach ($storages as $s) {
            $num = rtrim($s, 'gbt');
            $unit = str_replace($num, '', $s);
            $whens[] = "WHEN LOWER(title) REGEXP '{$num} ?{$unit}' THEN '{$s}'";
        }

        return 'CASE ' . implode(' ', $whens) . ' ELSE NULL END';
    }
}
