<?php

namespace app\Enums;

enum PurchaseSummaryDuration: string
    {
        case WEEK = 'week';
        case MONTH = 'month';
        case YEAR = 'year';
        case ALL = 'all';
        case CUSTOM = 'custom';
    }