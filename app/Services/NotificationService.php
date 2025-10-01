<?php

namespace app\Services;

use app\Models\Notification;

use app\Models\DowngradeUpgradeRequest;
use app\Models\ClientPackage;
use app\Models\Payment;
use app\Models\Offer;
use app\Models\WalletWithdrawalRequest;

use app\Enums\NotificationType;

/**
 * Notification service class
 */
class NotificationService
{

    public $read = null;

    private function getNotificationVals($notificationType, $client) { 

        $vals = [
            NotificationType::ASSET_UPGRADE_REQ->value => [
                "targetType" => DowngradeUpgradeRequest::$type,
                "message" => trim($client->name)." has requested for an asset Upgrade"
            ],
            NotificationType::ASSET_DOWNGRADE_REQ->value => [
                "targetType" => DowngradeUpgradeRequest::$type,
                "message" => trim($client->name)." has requested for an asset Downgrade"
            ],
            NotificationType::NEW_OFFER_APPROVAL_REQ->value => [
                "targetType" => Offer::$type,
                "message" => trim($client->name)." Created a new offer"
            ],
            NotificationType::OFFER_PAYMENT_CONF->value => [
                "targetType" => Payment::$type,
                "message" => trim($client->name)." has made payment for an offer awaiting approval"
            ],
            NotificationType::ORDER_COMPLETION->value => [
                "targetType" => ClientPackage::$type,
                "message" => trim($client->name)." has completed the purchase of an asset, please upload DOA"
            ],
            NotificationType::ORDER_PAYMENT_CONFIRMATION_REQ->value => [
                "targetType" => Payment::$type,
                "message" => trim($client->name)." has made a payment awaiting Confirmation"
            ],
            NotificationType::WALLET_WITHDRAWAL_REQ->value => [
                "targetType" => WalletWithdrawalRequest::$type,
                "message" => trim($client->name)." has triggered a Wallet Withdrawal Request awaiting approval"
            ]
        ];

        return $vals[$notificationType];
    }

    public function save($target, $notificationType)
    {
        $vals = $this->getNotificationVals($notificationType, $target->client);
        $notification = new Notification;
        $notification->notification_type = $notificationType;
        $notification->target_id = $target->id;
        $notification->target_type = $vals['targetType'];
        $notification->message = $vals['message'];

        $notification->save();

        return $notification;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);

        $notification->read = true;
        $notification->save();

        return $notification;
    }

    public function notification($id)
    {
        return Notification::find($id);
    }

    public function notifications()
    {
        $query = Notification::query();
        if($this->read != null) return Notification::where("read", $this->read);
        return $query->orderBy("created_at", "DESC")->get();
    }

}
