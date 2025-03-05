<?php

namespace app\Enums;

enum OrderType: string
    {
        case PURCHASE = 'purchase';
        case UPGRADE = 'upgrade';
    }