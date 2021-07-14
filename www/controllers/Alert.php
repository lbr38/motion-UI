<?php

namespace Controllers;

class Alert
{
    public function enable(string $status)
    {
        /**
         *  D'abord on récupère la configuration actuelle
         */
        $ini = $this->getConfiguration();

        /**
         *  Puis on écrase le paramètre alert_enable
         */
        if ($status == "yes") {
            $ini['alert_enable'] = "yes";
        } else {
            $ini['alert_enable'] = "no";
        }

        /**
         *  On écrit la configuration
         */
        \Controllers\Common::writeToIni($ini, ALERT_INI);
    }

    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd)
    {
        /**
         *  D'abord on récupère la configuration actuelle
         */
        $ini = $this->getConfiguration();

        /**
         *  Puis on écrase chaque paramètre
         */
        $ini['monday'] = $mondayStart . '-' . $mondayEnd;
        $ini['tuesday'] = $tuesdayStart . '-' . $tuesdayEnd;
        $ini['wednesday'] = $wednesdayStart . '-' . $wednesdayEnd;
        $ini['thursday'] = $thursdayStart . '-' . $thursdayEnd;
        $ini['friday'] = $fridayStart . '-' . $fridayEnd;
        $ini['saturday'] = $saturdayStart . '-' . $saturdayEnd;
        $ini['sunday'] = $sundayStart . '-' . $sundayEnd;

        /**
         *  On écrit la configuration
         */
        \Controllers\Common::writeToIni($ini, ALERT_INI);
    }

    public function getConfiguration()
    {
        return parse_ini_file(ALERT_INI);
    }

    public function getStatus()
    {
        $status = $this->getConfiguration();

        return $status['alert_enable'];
    }
}
