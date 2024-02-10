<?php

namespace Controllers;

use DateTime;

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
     *  Validate date
     */
    public static function validateDate(string $date, string $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     *  Vérifie que la chaine passée ne contient que des chiffres ou des lettres
     */
    public static function isAlphanum(string $data, array $additionalValidCaracters = [])
    {
        /**
         *  Si on a passé en argument des caractères supplémentaires à autoriser alors on les ignore dans le test en les remplacant temporairement par du vide
         */
        if (!empty($additionalValidCaracters)) {
            if (!ctype_alnum(str_replace($additionalValidCaracters, '', $data))) {
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
    public static function isAlphanumDash(string $data, array $additionalValidCaracters = [])
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
        $validCaracters = array('-', '_');

        /**
         *  Si on a passé en argument des caractères supplémentaires à autoriser alors on les ajoute à l'array $validCaracters
         */
        if (!empty($additionalValidCaracters)) {
            $validCaracters = array_merge($validCaracters, $additionalValidCaracters);
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

    /**
     *  Sort an array by the specified key
     */
    public static function groupBy($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    /**
     *  Return an array with the list of founded files in specified directory path
     */
    public static function findRecursive(string $directoryPath, string $fileExtension = null, bool $returnFullPath = true)
    {
        $foundedFiles = array();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directoryPath . '/', \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        /**
         *  Find files with specified extension
         */
        if (!empty($iterator)) {
            foreach ($iterator as $file) {
                /**
                 *  Skip '.' and '..' files
                 */
                if ($file->getFilename() == '.' || $file->getFilename() == '..') {
                    continue;
                }

                /**
                 *  Skip if the current file is a directory
                 */
                if ($file->isDir()) {
                    continue;
                }

                /**
                 *  If an extension has been specified, then check that the file has correct extension
                 */
                if (!empty($fileExtension)) {
                    /**
                     *  If extension is incorrect, then ignore the current file and process the next one
                     */
                    if ($file->getExtension() != $fileExtension) {
                        continue;
                    }
                }

                /**
                 *  By default, return file's fullpath
                 */
                if ($returnFullPath === true) {
                    $foundedFiles[] = $file->getPathname();
                /**
                 *  Else only return filename
                 */
                } else {
                    $foundedFiles[] = $file->getFilename();
                }
            }
        }

        return $foundedFiles;
    }

    /**
     *  Return an array with the list of founded directories in specified directory path
     *  Directory name can be filtered with a regex
     */
    public static function findDirRecursive(string $directoryPath, string $directoryNameRegex = null, bool $returnFullPath = true)
    {
        $foundedDirs = array();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directoryPath,
                \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        /**
         *  Find directories
         */
        if (!empty($iterator)) {
            foreach ($iterator as $file) {
                if (is_file($file->getPathname())) {
                    continue;
                }

                /**
                 *  Skip '.' and '..' files
                 */
                if ($file->getFilename() == '.' || $file->getFilename() == '..') {
                    continue;
                }

                /**
                 *  Skip if the current file is not a directory
                 */
                if (!$file->isDir()) {
                    continue;
                }

                /**
                 *  Skip if the current file is a symlink
                 */
                if ($file->isLink()) {
                    continue;
                }

                /**
                 *  Skip if the dir name does not match the specified regex
                 */
                if (!empty($directoryNameRegex)) {
                    if (!preg_match("/$directoryNameRegex/i", $file->getFilename())) {
                        continue;
                    }
                }

                /**
                 *  By default, return file's fullpath
                 */
                if ($returnFullPath === true) {
                    // trim last '..' and '.' characters
                    $foundedDir = rtrim($file->getPathname(), '.');
                /**
                 *  Else only return filename
                 */
                } else {
                    // trim last '..' and '.' characters
                    $foundedDir = rtrim($file->getFilename(), '.');
                }

                /**
                 *  Add founded directory to the array if not already in
                 */
                if (!in_array($foundedDir, $foundedDirs)) {
                    $foundedDirs[] = $foundedDir;
                }
            }
        }

        return $foundedDirs;
    }

    /**
     *  Convert bytes size to the most suitable human format (B, MB, GB...)
     */
    public static function sizeFormat($bytes, $returnFormat = true)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;

        if (($bytes >= 0) && ($bytes < $kb)) {
            $value = $bytes;
            $format = 'B';
        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            $value = ceil($bytes / $kb);
            $format = 'K';
        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            $value = ceil($bytes / $mb);
            $format = 'M';
        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            $value = ceil($bytes / $gb);
            $format = 'G';
        } elseif ($bytes >= $tb) {
            $value = ceil($bytes / $tb);
            $format = 'T';
        } else {
            $value = $bytes;
            $format = 'B';
        }

        if ($value >= 1000 and $value <= 1024) {
            $value = 1;

            if ($format == 'B') {
                $format = 'K';
            } elseif ($format == 'K') {
                $format = 'M';
            } elseif ($format == 'M') {
                $format = 'G';
            } elseif ($format == 'G') {
                $format = 'T';
            } elseif ($format == 'T') {
                $format = 'P';
            }
        }

        if ($returnFormat === true) {
            return $value . $format;
        }

        return $value;
    }
}