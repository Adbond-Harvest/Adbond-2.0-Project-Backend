<?php

namespace app\Enums;

enum UpgradeType: string
    {
        case ORDER = 'order';
        case ASSET = 'asset';
    }