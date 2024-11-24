<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssetsView extends Model
{
    protected $table = 'client_assets_view';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'client_id';
    public $incrementing = false;
}
