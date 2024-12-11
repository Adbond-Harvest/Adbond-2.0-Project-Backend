<?php

namespace app\Enums;

enum InterestReturnOccurrence: string
    {
        case MONTHLY = 'monthly';
        case BI_MONTHLY = 'bi-monthly';
        case QUARTERLY = 'quarterly';
        case SIX_MONTH = '6 Months';
        case ANNUALLY = 'annually';

    }