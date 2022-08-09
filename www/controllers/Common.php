<?php

namespace Controllers;

class Common
{
    /**
     *  Write to ini file
     */
    public static function writeToIni(array $configuration, string $iniFile)
    {
        $res = array();
        foreach ($configuration as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
                }
            } else {
                $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
            }
        }

        Common::safeFileRewrite($iniFile, implode("\r\n", $res));
    }

    public static function safeFileRewrite($fileName, $dataToSave)
    {
        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime(true);
            do {
                $canWrite = flock($fp, LOCK_EX);

                /**
                 *  If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                 */
                if (!$canWrite) {
                    usleep(round(rand(0, 100)*1000));
                }
            } while ((!$canWrite)and((microtime(true)-$startTime) < 5));

            /**
             *  File was locked so now we can store information
             */
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }

            fclose($fp);
        }
    }

    /**
     *  Validate form data
     */
    public static function validateData($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    /**
     *  Vérifie que la chaine passée ne contient que des chiffres ou des lettres
     */
    public static function isAlphanum(string $data, array $additionnalValidCaracters = [])
    {
        /**
         *  Si on a passé en argument des caractères supplémentaires à autoriser alors on les ignore dans le test en les remplacant temporairement par du vide
         */
        if (!empty($additionnalValidCaracters)) {
            if (!ctype_alnum(str_replace($additionnalValidCaracters, '', $data))) {
                return false;
            }

        /**
         *  Si on n'a pas passé de caractères supplémentaires alors on teste simplement la chaine avec ctype_alnum
         */
        } else {
            if (!ctype_alnum($data)) {
                ;
                return false;
            }
        }

        return true;
    }

    /**
     *  Vérifie que la chaine passée ne contient que des chiffres ou des lettres, un underscore ou un tiret
     *  Retire temporairement les tirets et underscore de la chaine passée afin qu'elle soit ensuite testée par la fonction PHP ctype_alnum
     */
    public static function isAlphanumDash(string $data, array $additionnalValidCaracters = [])
    {
        /**
         *  Si une chaine vide a été transmise alors c'est valide
         */
        if (empty($data)) {
            return true;
        }

        /**
         *  array contenant quelques exceptions de caractères valides
         */
        $validCaracters = array('-', '_', 'é', 'è', 'ê', 'à', 'ç', 'ï');

        /**
         *  Si on a passé en argument des caractères supplémentaires à autoriser alors on les ajoute à l'array $validCaracters
         */
        if (!empty($additionnalValidCaracters)) {
            $validCaracters = array_merge($validCaracters, $additionnalValidCaracters);
        }

        if (!ctype_alnum(str_replace($validCaracters, '', $data))) {
            return false;
        }

        return true;
    }

    /**
     *  Print a message
     */
    public static function printAlert(string $message, string $alertType = null)
    {
        if ($alertType == "error") {
            echo '<div class="alert-error">';
        }
        if ($alertType == "success") {
            echo '<div class="alert-success">';
        }
        if (empty($alertType)) {
            echo '<div class="alert">';
        } ?>

            <span><?= $message ?></span>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
                window.setTimeout(function() {
                    <?php
                    if ($alertType == "error" or $alertType == "success") {
                        echo "$('.alert-$alertType').fadeTo(1000, 0).slideUp(1000, function(){";
                    } else {
                        echo "$('.alert').fadeTo(1000, 0).slideUp(1000, function(){";
                    } ?>
                    $(this).remove();
                    });
                }, 2500);
            });
        </script>
        <?php
    }

    /**
     *  Reorder array to set desired value as first member or it
     */
    public static function arrayReorder(&$array, string $value)
    {
        $key = array_search($value, $array);

        if ($key) {
            unset($array[$key]);
        }

        array_unshift($array, $value);

        return array_unique($array);
    }

    /**
     *  Generate random number between 10000 and 99999
     */
    public static function generateRandom()
    {
        return mt_rand(10000, 99999);
    }
}