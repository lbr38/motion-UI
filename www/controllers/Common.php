<?php

namespace Controllers;

use DateTime;

class Common
{
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
     *  Check that the string passed contains only numbers or letters
     */
    public static function isAlphanum(string $data, array $additionalValidCaracters = [])
    {
        /**
         *  If a empty string has been passed then it's valid
         */
        if (empty($data)) {
            return true;
        }

        /**
         *  Array containing some exceptions of valid characters
         */
        $validCaracters = array('é', 'è', 'ê', 'à', 'â', 'ù', 'û', 'ç', 'ô', 'î', 'ï', 'ë', 'ü', 'ö', 'ä', 'ÿ', 'œ', 'æ');

        /**
         *  If we passed additional valid characters as argument then we add them to the $validCaracters array
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
     *  Check that the string passed contains only numbers, letters, an underscore or a dash
     */
    public static function isAlphanumDash(string $data, array $additionalValidCaracters = [])
    {
        return self::isAlphanum($data, array_merge($additionalValidCaracters, array('-', '_')));
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