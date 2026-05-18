<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureEmailVerificationSchemaCommand extends Command
{
    protected $signature = 'grocery:email-verification-schema';

    protected $description = 'Add email verification columns to USERS and VERIFICATION (Oracle) if missing';

    public function handle(): int
    {
        $conn = DB::connection('oracle');

        $this->addColumnIfMissing($conn, 'USERS', 'EMAIL_VERIFIED', 'NUMBER(1) DEFAULT 0 NOT NULL');
        $this->addColumnIfMissing($conn, 'USERS', 'EMAIL_VERIFIED_AT', 'DATE');

        $this->addColumnIfMissing($conn, 'VERIFICATION', 'VERIFICATION_CODE', 'VARCHAR2(6)');
        $this->addColumnIfMissing($conn, 'VERIFICATION', 'EMAIL', 'VARCHAR2(100)');
        $this->addColumnIfMissing($conn, 'VERIFICATION', 'PURPOSE', 'VARCHAR2(30)');
        $this->addColumnIfMissing($conn, 'VERIFICATION', 'STATUS', 'VARCHAR2(20)');

        $this->info('Email verification schema is ready.');
        $this->comment('Existing users are marked verified when you run: php scripts/apply-oracle-updates.php --skip-prices');

        return self::SUCCESS;
    }

    protected function addColumnIfMissing($conn, string $table, string $column, string $definition): void
    {
        $exists = $conn->selectOne(
            'SELECT COUNT(*) AS CNT FROM user_tab_columns WHERE table_name = ? AND column_name = ?',
            [strtoupper($table), strtoupper($column)]
        );

        if ((int) ($exists->cnt ?? $exists->CNT ?? 0) > 0) {
            $this->line("  skip {$table}.{$column} (exists)");

            return;
        }

        $conn->statement("ALTER TABLE {$table} ADD ({$column} {$definition})");
        $this->info("  added {$table}.{$column}");
    }
}
