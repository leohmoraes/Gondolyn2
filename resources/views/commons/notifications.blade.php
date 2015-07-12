<?php

    $notification = Session::get("notification");
    $notificationType = Session::get("notificationType");

    if ($notification) {
        if (AccountServices::isLoggedIn()) {
            echo AccountServices::inAppNotification($notification, $notificationType);
        } else {
            echo 'gondolynNotify("'.$notification.'", "'.$notificationType.'");';
        }
    }