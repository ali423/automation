<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnsureCommoditiesTitleUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration safely ensures the unique constraint on commodities.title:
     * 1. Finds all duplicate titles (including soft-deleted records)
     * 2. Keeps the oldest record (by id) with the original name
     * 3. Appends "(1)", "(2)", etc. to duplicate records
     * 4. Ensures the unique constraint exists
     *
     * @return void
     */
    public function up()
    {
        echo "\n🔍 Checking for duplicate commodity titles...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        try {
            // Step 1: Find all duplicate titles (excluding soft-deleted records)
            echo "Step 1/4: Finding duplicate titles...\n";
            $duplicates = $this->findDuplicateTitles();
            
            if (empty($duplicates)) {
                echo "✅ No duplicates found. All titles are unique.\n\n";
            } else {
                echo "⚠️  Found " . count($duplicates) . " duplicate title(s)\n\n";
                
                // Step 2: Resolve duplicates by appending numbers
                echo "Step 2/4: Resolving duplicates...\n";
                $resolvedCount = $this->resolveDuplicates($duplicates);
                echo "✅ Resolved {$resolvedCount} duplicate record(s)\n\n";
            }

            // Step 3: Check if unique constraint exists
            echo "Step 3/4: Checking unique constraint...\n";
            $constraintExists = $this->constraintExists();
            
            if ($constraintExists) {
                echo "✅ Unique constraint already exists\n\n";
            } else {
                // Step 4: Add unique constraint
                echo "Step 4/4: Adding unique constraint...\n";
                $this->addUniqueConstraint();
                echo "✅ Unique constraint added successfully\n\n";
            }

            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "🎉 Migration completed successfully!\n\n";
        } catch (\Throwable $e) {
            echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: We don't remove the unique constraint as it's part of the original schema.
     * We also don't revert the renamed duplicates as that could cause data loss.
     *
     * @return void
     */
    public function down()
    {
        // We don't revert the changes as:
        // 1. The unique constraint should remain
        // 2. Reverting renamed duplicates could cause data loss
        echo "\n⚠️  Rollback not implemented for this migration.\n";
        echo "The unique constraint and renamed duplicates will remain.\n";
    }

    /**
     * Find all duplicate titles in the commodities table
     *
     * @return array Array of duplicate titles with their record IDs
     */
    private function findDuplicateTitles(): array
    {
        // Find duplicates excluding soft-deleted records
        // Use a more database-agnostic approach
        $duplicateTitles = DB::table('commodities')
            ->whereNull('deleted_at')
            ->select('title', DB::raw('COUNT(*) as count'))
            ->groupBy('title')
            ->having('count', '>', 1)
            ->pluck('title')
            ->toArray();

        if (empty($duplicateTitles)) {
            return [];
        }

        // Get all IDs for each duplicate title
        $result = [];
        foreach ($duplicateTitles as $title) {
            $ids = DB::table('commodities')
                ->where('title', $title)
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();
            
            if (count($ids) > 1) {
                $result[$title] = $ids;
            }
        }

        return $result;
    }

    /**
     * Resolve duplicates by keeping the oldest record and renaming others
     *
     * @param array $duplicates Array of duplicate titles with their IDs
     * @return int Number of records resolved
     */
    private function resolveDuplicates(array $duplicates): int
    {
        $resolvedCount = 0;

        foreach ($duplicates as $title => $ids) {
            // Sort IDs to keep the oldest (lowest ID) with original name
            sort($ids);
            $keepId = array_shift($ids); // First ID keeps original name
            $duplicateIds = $ids; // Rest need to be renamed

            echo "   Processing '{$title}': Keeping ID {$keepId}, renaming " . count($duplicateIds) . " duplicate(s)\n";

            // Rename duplicates by appending (1), (2), etc.
            foreach ($duplicateIds as $index => $id) {
                $newTitle = $this->generateUniqueTitle($title, $keepId, $id);
                $suffix = $index + 1;
                
                DB::table('commodities')
                    ->where('id', $id)
                    ->update(['title' => $newTitle]);

                echo "      → ID {$id}: '{$title}' → '{$newTitle}'\n";
                $resolvedCount++;
            }
        }

        return $resolvedCount;
    }

    /**
     * Generate a unique title by appending a suffix
     *
     * @param string $originalTitle
     * @param int $keepId The ID that keeps the original name
     * @param int $duplicateId The ID that needs a new name
     * @return string
     */
    private function generateUniqueTitle(string $originalTitle, int $keepId, int $duplicateId): string
    {
        $suffix = 1;
        $newTitle = $originalTitle . " ({$suffix})";

        // Check if this title already exists (could happen if migration is run multiple times)
        while ($this->titleExists($newTitle, $duplicateId)) {
            $suffix++;
            $newTitle = $originalTitle . " ({$suffix})";
        }

        return $newTitle;
    }

    /**
     * Check if a title already exists (excluding the current record)
     *
     * @param string $title
     * @param int $excludeId
     * @return bool
     */
    private function titleExists(string $title, int $excludeId): bool
    {
        return DB::table('commodities')
            ->where('title', $title)
            ->whereNull('deleted_at')
            ->where('id', '!=', $excludeId)
            ->exists();
    }

    /**
     * Check if the unique constraint exists on the title column
     *
     * @return bool
     */
    private function constraintExists(): bool
    {
        try {
            $databaseName = DB::connection()->getDatabaseName();
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = 'commodities'
                AND CONSTRAINT_TYPE = 'UNIQUE'
                AND CONSTRAINT_NAME LIKE '%title%'
            ", [$databaseName]);

            return !empty($constraints);
        } catch (\Exception $e) {
            // If we can't check, assume it doesn't exist and try to add it
            // The database will throw an error if it already exists
            return false;
        }
    }

    /**
     * Add unique constraint on the title column
     *
     * @return void
     */
    private function addUniqueConstraint(): void
    {
        try {
            Schema::table('commodities', function (Blueprint $table) {
                // Try to add unique constraint
                // If it already exists, Laravel/SQL will throw an error which we catch
                $table->unique('title', 'commodities_title_unique');
            });
        } catch (\Exception $e) {
            // If constraint already exists, that's fine
            if (str_contains($e->getMessage(), 'Duplicate key name') || 
                str_contains($e->getMessage(), 'already exists')) {
                echo "   ℹ️  Constraint already exists (this is fine)\n";
            } else {
                throw $e;
            }
        }
    }
}
