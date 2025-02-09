<?php

namespace app\Enums;

enum OfferApprovalStatus: string
    {
        case PENDING = 'pending';
        case APPROVED = 'approved';
        case REJECTED = 'rejected';
    }