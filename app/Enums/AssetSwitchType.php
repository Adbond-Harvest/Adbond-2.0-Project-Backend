<?php

namespace app\Enums;

enum AssetSwitchType: string
    {
        case UPGRADE = 'upgrade';
        case DOWNGRADE = 'downgrade';
    }