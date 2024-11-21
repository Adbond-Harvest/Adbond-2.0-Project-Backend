<?php

namespace app\Enums;

enum CommissionTransactionType: string
    {
        case EARNING = "app\Models\StaffCommissionEarning";
        case REDEMPTION = "app\Models\StaffCommissionRedemption";
    }