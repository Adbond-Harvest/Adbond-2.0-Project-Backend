<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\ReadNotification;

use app\Http\Resources\NotificationResource;

use app\Services\NotificationService;

use app\Utilities;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

    public function unreadNotifications(Request $request)
    {
        $this->notificationService->read = 0;
        $notifications = $this->notificationService->notifications();

        return Utilities::ok(NotificationResource::collection($notifications));
    }

    public function read(ReadNotification $request)
    {
        try{
            $notification = $this->notificationService->notification($request->validated("id"));
            if(!$notification) return Utilities::error402("Notification not found");
            
            $this->notificationService->markAsRead($notification);

            return Utilities::okay("Successful");
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }
}
