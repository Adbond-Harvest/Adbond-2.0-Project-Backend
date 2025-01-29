<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPackageSummaryView extends Model
{
    protected $table = 'project_packages_summary_view';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
