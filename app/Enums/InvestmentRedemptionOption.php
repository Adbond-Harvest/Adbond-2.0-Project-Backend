<?php

namespace app\Enums;

enum InvestmentRedemptionOption: string
    {
        case PROFIT_ONLY = 'profit-only';
        case PROPERTY = 'property';
        case CASH = 'cash';
    }