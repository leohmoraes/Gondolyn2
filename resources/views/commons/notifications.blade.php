<?php

    $notification = Session::get("notification");

    if ($notification) {
        if (AccountServices::isLoggedIn()) {
            echo AccountServices::inAppNotification($notification);
        } else {
            echo 'gondolynNotify("'.$notification.'");';
        }
    }