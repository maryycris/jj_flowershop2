<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomBouquet;
use App\Services\CustomBouquetImageService;
use Illuminate\Support\Facades\DB;

class RegenerateBouquetPreviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bouquets:regenerate-previews {--force : Force regeneration even if preview exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate preview images for all custom bouquets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!extension_loaded('gd')) {
            $this->error('GD extension is not available. Cannot generate preview images.');
            return 1;
        }

        $force = $this->option('force');
        
        $query = CustomBouquet::where('bouquet_type', 'regular');
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('preview_image')
                  ->orWhere('preview_image', '');
            });
        }

        $bouquets = $query->get();
        
        if ($bouquets->isEmpty()) {
            $this->info('No bouquets found to regenerate.');
            return 0;
        }

        $this->info("Found {$bouquets->count()} bouquet(s) to process...");
        
        $imageService = new CustomBouquetImageService();
        $successCount = 0;
        $errorCount = 0;
        
        $bar = $this->output->createProgressBar($bouquets->count());
        $bar->start();

        foreach ($bouquets as $bouquet) {
            try {
                // Delete old preview if exists
                if ($bouquet->preview_image && file_exists(storage_path('app/public/' . $bouquet->preview_image))) {
                    @unlink(storage_path('app/public/' . $bouquet->preview_image));
                }
                
                $previewPath = $imageService->generateCompositeImage($bouquet);
                
                if ($previewPath && file_exists(storage_path('app/public/' . $previewPath))) {
                    DB::table('custom_bouquets')
                        ->where('id', $bouquet->id)
                        ->update(['preview_image' => $previewPath]);
                    $successCount++;
                } else {
                    $errorCount++;
                    $this->newLine();
                    $this->warn("Failed to generate preview for bouquet #{$bouquet->id}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error processing bouquet #{$bouquet->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Successfully regenerated: {$successCount}");
        if ($errorCount > 0) {
            $this->warn("✗ Errors: {$errorCount}");
        }

        return 0;
    }
}
