<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Meter extends Model
{

    protected static function booted()
    {
        static::updating(function ($meter) {
            $oldContent = $meter->getOriginal('remarks');
            $newContent = $meter->remarks;
            preg_match_all('/src="([^"]+)"/', $oldContent, $oldMatches);
            preg_match_all('/src="([^"]+)"/', $newContent, $newMatches);
            $oldFiles = $oldMatches[1] ?? [];
            $newFiles = $newMatches[1] ?? [];

            // Find deleted files
            $deletedFiles = array_diff($oldFiles, $newFiles);
            foreach ($deletedFiles as $url) {
                // Convert full URL to storage path
                $path = str_replace(asset('storage') . '/', '', $url);

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    } 
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
