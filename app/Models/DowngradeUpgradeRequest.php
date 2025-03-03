<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DowngradeUpgradeRequest extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function packageFrom()
    {
        return $this->belongsTo(Package::class, "from_package_id", "id");
    }

    public function packageTo()
    {
        return $this->belongsTo(Package::class, "to_package_id", "id");
    }

    public function asset()
    {
        return $this->belongsTo(ClientPackage::class, "client_package_id", "id");
    }
}
