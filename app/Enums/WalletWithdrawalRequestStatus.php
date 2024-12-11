<?php

namespace app\Enums;

enum WalletWithdrawalRequestStatus: string
    {
        case PENDING = 'pending';
        case APPROVED = 'approved';
        case REJECTED = 'rejected';
    }