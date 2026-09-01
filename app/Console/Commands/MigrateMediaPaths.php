<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MigrateMediaPaths extends Command
{
    protected $signature = 'media:migrate-paths {--chunk=200} {--dry-run}';
    protected $description = 'Migrate Spatie media files from numeric directories to model-based paths (including conversions/responsive).';

    public function handle(): int
    {
        $chunk  = (int) $this->option('chunk');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Media migration started (chunk={$chunk}, dry-run=" . ($dryRun ? 'yes' : 'no') . ')');

        Media::query()
            ->orderBy('id')
            ->chunkById($chunk, function ($medias) use ($dryRun) {
                foreach ($medias as $media) {
                    $this->migrateMedia($media, $dryRun);
                }
            });

        $this->cleanupOldNumericDirectories($dryRun);

        $this->info('Media migration finished.');
        return self::SUCCESS;
    }

    protected function migrateMedia(Media $media, bool $dryRun): void
    {
        $diskName = $media->disk ?: 'public';
        $disk = Storage::disk($diskName);

        $modelFolder = Str::plural(Str::snake(class_basename($media->model_type)));
        $baseDir     = "{$modelFolder}/{$media->model_id}";

        // مطابق PathGenerator شما:
        $newFilePath = "{$baseDir}/{$media->file_name}";
        $newConvDir  = "{$baseDir}/conversions";
        $newRespDir  = "{$baseDir}/responsive";

        // مسیرهای قدیمی:
        $oldFilePath = "{$media->id}/{$media->file_name}";
        $oldConvDir  = "{$media->id}/conversions";

        // responsive قدیمی‌ها ممکنه responsive-images بوده باشه
        $oldRespDirCandidates = [
            "{$media->id}/responsive",
            "{$media->id}/responsive-images",
        ];

        // 1) فایل اصلی
        $this->moveIfExists($diskName, $oldFilePath, $newFilePath, $dryRun);

        // 2) conversions (اگر پوشه/فایل داشت)
        $this->moveDirectoryContentsIfExists($diskName, $oldConvDir, $newConvDir, $dryRun);

        // 3) responsive (هر کدوم که وجود داشت)
        foreach ($oldRespDirCandidates as $oldRespDir) {
            $this->moveDirectoryContentsIfExists($diskName, $oldRespDir, $newRespDir, $dryRun);
        }
    }

    /**
     * Move single file if source exists and destination doesn't.
     */
    protected function moveIfExists(string $diskName, string $oldPath, string $newPath, bool $dryRun): void
    {
        $disk = Storage::disk($diskName);

        if (!$disk->exists($oldPath)) {
            return;
        }

        if ($disk->exists($newPath)) {
            // قبلاً منتقل شده
            return;
        }

        $newDir = Str::beforeLast($newPath, '/');

        $this->line("FILE: {$diskName}: {$oldPath}  →  {$newPath}");

        if ($dryRun) {
            return;
        }

        try {
            $disk->makeDirectory($newDir);

            $ok = $disk->move($oldPath, $newPath);

            // بعضی موارد move ممکنه false برگردونه
            if ($ok === false) {
                $this->error("FAILED(move=false): {$diskName}: {$oldPath}  →  {$newPath}");
                return;
            }

            // اطمینان
            if ($disk->exists($oldPath) && !$disk->exists($newPath)) {
                $this->error("FAILED(verify): {$diskName}: {$oldPath} still exists, dest missing");
            }
        } catch (\Throwable $e) {
            $this->error("FAILED(exception): {$diskName}: {$oldPath}  →  {$newPath} | " . $e->getMessage());
        }
    }

    /**
     * Move all files in a directory to another directory (keeping filenames).
     * This avoids relying on adapter's moveDirectory support.
     */
    protected function moveDirectoryContentsIfExists(string $diskName, string $oldDir, string $newDir, bool $dryRun): void
    {
        $disk = Storage::disk($diskName);

        // اگر اصلاً پوشه وجود نداره یا فایلی داخلش نیست
        $files = $disk->allFiles($oldDir);
        if (empty($files)) {
            return;
        }

        $this->line("DIR:  {$diskName}: {$oldDir}/  →  {$newDir}/  (" . count($files) . " files)");

        if ($dryRun) {
            return;
        }

        try {
            $disk->makeDirectory($newDir);

            foreach ($files as $oldFile) {
                // $oldFile مثل "126/conversions/thumb.jpg"
                $filename = basename($oldFile);
                $newFile = "{$newDir}/{$filename}";

                // اگر مقصد وجود داشت، این یکی رو رد کن
                if ($disk->exists($newFile)) {
                    continue;
                }

                $ok = $disk->move($oldFile, $newFile);

                if ($ok === false) {
                    $this->error("FAILED(move=false): {$diskName}: {$oldFile} → {$newFile}");
                }
            }

            // اگر پوشه قدیمی خالی شد حذفش کن
            if (empty($disk->allFiles($oldDir))) {
                $disk->deleteDirectory($oldDir);
            }
        } catch (\Throwable $e) {
            $this->error("FAILED(exception): {$diskName}: {$oldDir} → {$newDir} | " . $e->getMessage());
        }
    }

    /**
     * Cleanup numeric dirs that are empty (on public disk).
     * You can extend this to loop through unique disks if needed.
     */
    protected function cleanupOldNumericDirectories(bool $dryRun): void
    {
        $diskName = 'public';
        $disk = Storage::disk($diskName);

        $directories = $disk->directories();

        foreach ($directories as $dir) {
            if (!ctype_digit(basename($dir))) {
                continue;
            }

            if (empty($disk->allFiles($dir))) {
                $this->line("Delete empty dir: {$diskName}: {$dir}");

                if (!$dryRun) {
                    $disk->deleteDirectory($dir);
                }
            }
        }
    }
}
