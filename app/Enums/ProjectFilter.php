<?php

namespace App\Enums;

enum ProjectFilter: string
    {
        case ACTIVE = "active";
        case INACTIVE = "inactive";
    }