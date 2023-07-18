<?php

namespace Models\Motion;

use Exception;

class Alert extends \Models\Model
{
    /**
     *  Returns actual alerts time slots configuration
     */
    public function getConfiguration()
    {
        $config = array();

        $result = $this->db->query("SELECT * FROM alerts");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $config = $row;
        }

        return $config;
    }

    /**
     *  Enable / disable motion alerts
     */
    public function enable(string $status)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET Status = :status");
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }

    /**
     *  Configure motion alerts
     */
    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET
        Recipient = :mailRecipient,
        Monday_start = :mondayStart,
        Monday_end = :mondayEnd,
        Tuesday_start = :tuesdayStart,
        Tuesday_end = :tuesdayEnd,
        Wednesday_start = :wednesdayStart,
        Wednesday_end = :wednesdayEnd,
        Thursday_start = :thursdayStart,
        Thursday_end = :thursdayEnd,
        Friday_start = :fridayStart,
        Friday_end = :fridayEnd,
        Saturday_start = :saturdayStart,
        Saturday_end = :saturdayEnd,
        Sunday_start = :sundayStart,
        Sunday_end = :sundayEnd");
        $stmt->bindValue(':mailRecipient', $mailRecipient);
        $stmt->bindValue(':mondayStart', $mondayStart);
        $stmt->bindValue(':mondayEnd', $mondayEnd);
        $stmt->bindValue(':tuesdayStart', $tuesdayStart);
        $stmt->bindValue(':tuesdayEnd', $tuesdayEnd);
        $stmt->bindValue(':wednesdayStart', $wednesdayStart);
        $stmt->bindValue(':wednesdayEnd', $wednesdayEnd);
        $stmt->bindValue(':thursdayStart', $thursdayStart);
        $stmt->bindValue(':thursdayEnd', $thursdayEnd);
        $stmt->bindValue(':fridayStart', $fridayStart);
        $stmt->bindValue(':fridayEnd', $fridayEnd);
        $stmt->bindValue(':saturdayStart', $saturdayStart);
        $stmt->bindValue(':saturdayEnd', $saturdayEnd);
        $stmt->bindValue(':sundayStart', $sundayStart);
        $stmt->bindValue(':sundayEnd', $sundayEnd);
        $stmt->execute();
    }
}
