<?php

namespace app\Enums;

enum ProductType: string
    {
        case PACKAGE = "app\Models\Package";
        case PROJECT = "app\Models\Project";
    }