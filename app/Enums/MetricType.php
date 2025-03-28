<?php

namespace app\Enums;

enum MetricType: string
    {
        case TOTAL = 'total';
        case ACTIVE = 'active';
        case BOTH = 'both';
    }