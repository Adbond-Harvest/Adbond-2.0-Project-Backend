<?php

namespace app\Enums;

enum MetricDuration: string
    {
        case TODAY = 'today';
        case WEEK = 'week';
        case MONTH = 'month';
        case YEAR = 'year';
    }