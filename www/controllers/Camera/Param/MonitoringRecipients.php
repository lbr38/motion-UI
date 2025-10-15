<?php

namespace Controllers\Camera\Param;

use Controllers\Utils\Validate;
use Exception;

class MonitoringRecipients
{
    /**
     *  Check that monitoring recipients are valid emails
     */
    public static function check(array $recipients) : void
    {
        foreach ($recipients as $recipient) {
            if (!Validate::email($recipient)) {
                throw new Exception('Monitoring recipient "' . $recipient . '" is not a valid email');
            }
        }
    }
}
