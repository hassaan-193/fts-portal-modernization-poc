<?php

namespace App\Services;

use Storage;
use App\User;
use App\Models\Notification;
use App\Services\PushNotificationService;

/**
 * Notification classs
 *
 * This class can be used to send push notifications.
 * This library needs php-curl extension
 * If someone using laravel then can set config.firebase for server key and push url
 *
 */
class PushNotification {

    public function sendBookingNoification($msg, $firebase_token, $data=[] ) {
        $this->sendNotification($msg, $firebase_token, $data);
    }

    public function sendNotification($msg, $firebase_token, $data = []) {

        $pushManager = new PushNotificationService();
        $message = $msg;

        // $unread_count=$this->getUnreadNotificationCount($firebase_token);

        return $pushManager->setTitle('FTS Request')
                ->setBody($msg)
                ->setPriority('High')
                ->setBadge(1)
                ->setSound('default')
                ->setContentAvailable(true)
                ->setDeviceTokens($firebase_token)
                ->setCustomPayload([
                    "action" => 'com.mawshd.tyre',
                    "body" => $msg,
                    "title" => 'Fts-Request',
                    "data" => $data,

                ])
                ->push();

        // adding notification to db
        // $this->addNotificationToDb($firebase_token, $msg);
    }

    public function sendTestNoification($msg, $firebase_token, $data = []) {

        $pushManager = new PushNotificationService();
        $message = $msg;

        $response = $pushManager->setTitle(config('app.name'))
                ->setBody($msg)
                ->setPriority('High')
                ->setBadge(100)
                ->setSound('default')
                ->setContentAvailable(true)
                ->setDeviceTokens($firebase_token)
                ->setCustomPayload([
                    "action" => config('tyre.app_bundle_name'),
                    "body" => $msg,
                    "title" => config('app.name'),
                    'data' => $data,

                ])
                ->push();
        return $response;
    }

    private function addNotificationToDb($firebase_token, $msg) {

        // Get the user id based on firebase_token
        $user = User::where('firebase_token', $firebase_token)->get();

        //echo "Firebase_token: ".$firebase_token;

        if ($user != null && sizeof($user) > 0) {
            $user = $user[0];

            $notification_data = [
                'user_id' => $user->id,
                'del_flag' => 0,
                'notification' => $msg,
            ];
            //var_dump($notification_data);
            $notification = Notification::create($notification_data);
            $notification->save();
        }
    }

    public function getUnreadNotificationCount($firebase_token) {

        $count=0;
        // Get the user id based on firebase_token
        $user = User::where('firebase_token', $firebase_token)->get();

        if ($user != null && sizeof($user) > 0) {
            $user = $user[0];

            // get the unread count from notifications db
            $count = Notification::where('user_id', $user->id)
                    ->where('read_flag', 0)
                    ->where('del_flag', 0)->get()->count();

        }

        return $count;
    }

}
