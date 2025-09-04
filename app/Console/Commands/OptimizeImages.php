<?php

// app/Console/Commands/OptimizeImages.php
namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--quality=80 : Image quality (1-100)}';
    protected $description = 'Optimize product images for better performance';

    public function handle()
    {
        $quality = $this->option('quality');
        
        $this->info('Optimizing product images...');

        $images = ProductImage::all();
        $optimizedCount = 0;

        foreach ($images as $productImage) {
            try {
                $imagePath = str_replace('/storage/', '', $productImage->image_url);
                
                if (Storage::disk('public')->exists($imagePath)) {
                    $image = Image::make(Storage::disk('public')->path($imagePath));
                    
                    // Get original file size
                    $originalSize = Storage::disk('public')->size($imagePath);
                    
                    // Optimize image
                    $image->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    
                    $image->save(null, $quality);
                    
                    // Get new file size
                    $newSize = Storage::disk('public')->size($imagePath);
                    
                    $savedBytes = $originalSize - $newSize;
                    $savedPercent = round(($savedBytes / $originalSize) * 100, 1);
                    
                    $this->line("Optimized: {$productImage->image_url} - Saved {$savedPercent}% ({$savedBytes} bytes)");
                    $optimizedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error optimizing {$productImage->image_url}: " . $e->getMessage());
            }
        }

        $this->info("Optimized {$optimizedCount} images.");
        return 0;
    }
}