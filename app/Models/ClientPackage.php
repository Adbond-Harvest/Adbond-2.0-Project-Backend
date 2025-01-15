<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use app\Services\FileService;

class ClientPackage extends Model
{
    use HasFactory;

    public static $type = "app\Models\ClientPackage";

    /**
     * Get the parent purchase model (Order or Offer).
     */
    public function purchase(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all files associated with the ClientPackage.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'belongs');
    }

    /**
     * Relationship to Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship to Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($clientPackage) {
            if($clientPackage->contract_file_id) self::updateFile($clientPackage->contract_file_id, $clientPackage);
            if($clientPackage->happiness_letter_file_id) self::updateFile($clientPackage->happiness_letter_file_id, $clientPackage);
            if($clientPackage->doa_file_id) self::updateFile($clientPackage->doa_file_id, $clientPackage);
        });

    }

    private static function updateFile($fileId, $clientPackage)
    {
        $fileService = new FileService;
        $file = $fileService->getFile($fileId);
        if($file && (!$file->belongs_id || !$file->belongs_type)){
            $fileMeta = ["belongsId"=>$clientPackage->id, "belongsType"=>self::$type];
            $fileService->updateFileObj($fileMeta, $file);
        }
    }
    
}
