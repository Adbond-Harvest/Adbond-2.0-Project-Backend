<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\Notification;
use app\Models\Client;

use app\Enums\NotificationType;

class AddUserToNotifications extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = Notification::all();

        if($notifications->count() > 0) {
            foreach($notifications as $notification) {
                $client = null;
                switch($notification->notification_type) {
                    case NotificationType::WALLET_WITHDRAWAL_REQ->value :
                        $client = $notification->target->wallet->client; break;
                    default :
                        $client = $notification->target->client; break;
                }
                $notification->user_type = Client::$userType;
                $notification->user_id = $client->id;
                $notification->update();
            }
                
        }
    }
}
