<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = "files";

    public function packagePhoto()
    {
        return $this->hasOne(PackagePhoto::class, "photo_id", "id");
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (File $file) {
            //Storage::disk('s3')->delete($file->url);
            cloudinary()->uploadApi()->destroy([$file->public_id]);

            if($file->packagePhoto) $file->packagePhoto->delete();
        });
    }
}
