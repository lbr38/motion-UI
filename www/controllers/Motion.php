<?php

namespace Controllers;

use Exception;

class Motion
{
    /**
     *  Returns motion service status
     */
    public function getStatus()
    {
        $motionStatus = shell_exec('/usr/sbin/service motion status');
        if (preg_match('/Active: active/', $motionStatus)) {
            return 'started';
        }
        if (preg_match('/Active: inactive/', $motionStatus)) {
            return 'stopped';
        }
    }

    /**
     *  Start or stop motion capture
     */
    public function startStop(string $status)
    {
        if ($status == 'start') {
            exec("sudo /usr/sbin/service motion start");
        }
        if ($status == 'stop') {
            exec("sudo /usr/sbin/service motion stop");
        }
    }

    /**
     *  Configure motion
     */
    public function configure(string $filename, array $options)
    {
        $filename = \Controllers\Common::validateData($filename);
        $content = '';

        foreach ($options as $option) {
            $optionStatus = \Controllers\Common::validateData($option['status']);
            $optionName = \Controllers\Common::validateData($option['name']);
            $optionValue = $option['value'];

            /**
             *  Comment the parameter with a semicolon in the final file if status sent is not 'enabled'
             */
            if ($optionStatus == 'enabled') {
                $optionStatus = '';
            } else {
                $optionStatus = ';';
            }

            /**
             *  On vérifie que le nom de l'option est valide, càd qu'il ne contient pas de caractère spéciaux
             */
            if (\Controllers\Common::isAlphanumDash($optionName) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            if (\Controllers\Common::isAlphanumDash($optionValue, array('.', ' ', ',', ':', '/', '\\', '%', '(', ')', '=', '\'', '[', ']', '@')) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            /**
             *  Si il n'y a pas eu d'erreurs jusque là alors on forge la ligne du paramètre avec son nom et sa valeur, séparés par un égal '='
             *  Sinon on forge la même ligne mais en laissant la valeur vide afin que l'utilisateur puisse la resaisir
             */
            $content .= $optionStatus . $optionName . " " . $optionValue . PHP_EOL . PHP_EOL;
        }

        /**
         *  Enfin, on écrit le contenu dans le fichier spécifié
         */
        if (file_exists('/etc/motion/' . $filename)) {
            file_put_contents('/etc/motion/' . $filename, $content);
        }
        unset($content);
    }
}
