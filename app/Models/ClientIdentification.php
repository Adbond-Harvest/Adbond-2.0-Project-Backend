<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientIdentification extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function identification()
    {
        return $this->belongsTo(Identification::class);
    }

    public function photo()
    {
        return $this->belongsTo(File::class, "file_id", "id");
    }
}
