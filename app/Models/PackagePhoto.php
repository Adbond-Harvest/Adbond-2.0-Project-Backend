<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePhoto extends Model
{
    use HasFactory;

    public function photo()
    {
        return $this->belongsTo(File::class, "photo_id", "id");
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (PackagePhoto $packagePhoto) {
            //Storage::disk('s3')->delete($file->url);
            // $packagePhoto->photo->delete();
        });
        self::deleted(function (PackagePhoto $packagePhoto) {
            //
        });
    }
}
