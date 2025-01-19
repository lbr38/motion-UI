<?php

namespace Controllers\App\Config;

use Exception;

class Notification
{
    public static function get()
    {
        $NOTIFICATION = 0;
        $NOTIFICATION_MESSAGES = array();
        $mynotification = new \Controllers\Notification();

        /**
         *  Retrieve unread notifications from database
         */
        $NOTIFICATION_MESSAGES = $mynotification->getUnread();
        $NOTIFICATION = count($NOTIFICATION_MESSAGES);

        /**
         *  If an update is available, generate a new notification
         */
        if (UPDATE_AVAILABLE) {
            $message = '<p>A new release is available: <a href="' . PROJECT_GIT_REPO . '/releases/latest" target="_blank" rel="noopener noreferrer" title="See changelog"><code>' . GIT_VERSION . '</code> <img src="/assets/icons/external-link.svg" class="icon-small" /></a></p>';
            $message .= '<p>Please update your docker image by following the steps documented <b><a href="' . PROJECT_UPDATE_DOC_URL . '" target="_blank" rel="noopener noreferrer"><code>here</code></b> <img src="/assets/icons/external-link.svg" class="icon-small" /></a></p>';

            $NOTIFICATION_MESSAGES[] = array('Title' => 'Update available', 'Message' =>  $message);
            $NOTIFICATION++;
        }

        if (!defined('NOTIFICATION')) {
            define('NOTIFICATION', $NOTIFICATION);
        }

        if (!defined('NOTIFICATION_MESSAGES')) {
            define('NOTIFICATION_MESSAGES', $NOTIFICATION_MESSAGES);
        }

        unset($NOTIFICATION, $NOTIFICATION_MESSAGES, $mynotification);
    }
}
