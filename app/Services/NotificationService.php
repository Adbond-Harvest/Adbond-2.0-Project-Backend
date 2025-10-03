<?php

namespace app\Services;

use app\Models\Notification;

use app\Models\DowngradeUpgradeRequest;
use app\Models\ClientPackage;
use app\Models\Payment;
use app\Models\Offer;
use app\Models\WalletWithdrawalRequest;
use app\Models\User;
use app\Models\Client;

use app\Enums\NotificationType;

/**
 * Notification service class
 */
class NotificationService
{

    public $read = null;

    private function getNotificationVals($notificationType, $user) { 

        $vals = [
            NotificationType::ASSET_UPGRADE_REQ->value => [
                "targetType" => DowngradeUpgradeRequest::$type,
                "message" => trim($user->name)." has requested for an asset Upgrade",
                "userType" => CLient::$userType
            ],
            NotificationType::ASSET_DOWNGRADE_REQ->value => [
                "targetType" => DowngradeUpgradeRequest::$type,
                "message" => trim($user->name)." has requested for an asset Downgrade",
                "userType" => CLient::$userType
            ],
            NotificationType::NEW_OFFER_APPROVAL_REQ->value => [
                "targetType" => Offer::$type,
                "message" => trim($user->name)." Created a new offer",
                "userType" => CLient::$userType
            ],
            NotificationType::OFFER_PAYMENT_CONF->value => [
                "targetType" => Payment::$type,
                "message" => trim($user->name)." has made payment for an offer awaiting approval",
                "userType" => CLient::$userType
            ],
            NotificationType::ORDER_COMPLETION->value => [
                "targetType" => clientPackage::$type,
                "message" => trim($user->name)." has completed the purchase of an asset, please upload DOA",
                "userType" => CLient::$userType
            ],
            NotificationType::ORDER_PAYMENT_CONFIRMATION_REQ->value => [
                "targetType" => Payment::$type,
                "message" => trim($user->name)." has made a payment awaiting Confirmation",
                "userType" => CLient::$userType
            ],
            NotificationType::WALLET_WITHDRAWAL_REQ->value => [
                "targetType" => WalletWithdrawalRequest::$type,
                "message" => trim($user->name)." has triggered a Wallet Withdrawal Request awaiting approval",
                "userType" => CLient::$userType
            ]
        ];

        return $vals[$notificationType];
    }

    /**
     * User can be user or client
     */
    public function save($target, $notificationType, $user)
    {
        $vals = $this->getNotificationVals($notificationType, $user);
        $notification = new Notification;
        $notification->notification_type = $notificationType;
        $notification->target_id = $target->id;
        $notification->target_type = $vals['targetType'];
        $notification->message = $vals['message'];
        $notification->user_type = $vals['userType'];
        $notification->user_id = $user->id;

        $notification->save();

        return $notification;
    }

    public function markAsRead($notification)
    {
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
