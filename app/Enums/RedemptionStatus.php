<?php

namespace app\Enums;

enum RedemptionStatus: string
    {
        case PENDING = 'pending';
        case COMPLETED = 'completed';
        case REJECTED = 'rejected';
    }