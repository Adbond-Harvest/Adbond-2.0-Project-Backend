<?php

namespace app\Services;

use app\Models\ResellOrder;


class ResellOrderService
{
    public function resellOrder($id)
    {
        return ResellOrder::find($id);
    }
}