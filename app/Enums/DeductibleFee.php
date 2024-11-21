<?php

namespace app\Enums;

enum DeductibleFee: string
    {
        case COMMISSION_TAX = "commission tax";
        case DOWNGRADE_PENALTY = "downgrade penalty";
    }