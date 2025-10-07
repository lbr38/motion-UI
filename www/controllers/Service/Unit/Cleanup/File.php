<?php

namespace Controllers\Service\Unit\Cleanup;

use Exception;
use \Controllers\Filesystem\Directory;

class File extends \Controllers\Service\Service
{
    public function __construct(string $unit)
    {
        parent::__construct($unit);
    }

    /**
     *  Clean temporary files
     */
    public function run() : void
    {
        /**
         *  Clean service units logs older than 15 days
         */
        if (is_dir(SERVICE_LOGS_DIR)) {
            parent::log('Cleaning service logs...');

            $files = \Controllers\Filesystem\File::findRecursive(SERVICE_LOGS_DIR, ['log']);

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-15 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean autostart logs older than 7 days
         */
        if (is_dir(AUTOSTART_LOGS_DIR)) {
            parent::log('Cleaning autostart logs...');

            $files = glob(AUTOSTART_LOGS_DIR . '/*.log');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-7 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean go2rtc logs older than 7 days
         */
        if (is_dir(GO2RTC_DIR . '/logs')) {
            parent::log('Cleaning go2rtc logs...');

            $files = glob(GO2RTC_DIR . '/logs/*.log');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-7 days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete log file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }
        }

        /**
         *  Clean timelapse images older than 30 days
         */
        if (is_dir(CAMERAS_TIMELAPSE_DIR)) {
            parent::log('Cleaning timelapse images...');

            // Calculate the date before which timelapse images should be deleted
            $date = date('Y-m-d', strtotime('-' . TIMELAPSE_RETENTION . ' days'));

            // Get all timelapse directories
            $dirs = glob(CAMERAS_TIMELAPSE_DIR . '/*/*', GLOB_ONLYDIR);

            if (!empty($dirs)) {
                foreach ($dirs as $dir) {
                    $directoryDate = basename($dir);

                    // Skip directory if it is not a date
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $directoryDate)) {
                        continue;
                    }

                    // Skip directory if it is not older than specified date
                    if ($directoryDate >= $date) {
                        continue;
                    }

                    // Delete directory
                    if (!Directory::deleteRecursive($dir)) {
                        throw new Exception('Failed to delete directory ' . $dir);
                    }

                    parent::log('Directory ' . $dir . ' deleted');
                }
            }
        }

        /**
         *  Clean motion event files
         */
        if (is_dir(CAPTURES_DIR)) {
            parent::log('Cleaning motion event files...');

            // Get all event files
            $files = glob(CAPTURES_DIR . '/*/*/*/*');

            // Get all movies directories
            $moviesDirs  = glob(CAPTURES_DIR . '/*/*/movies', GLOB_ONLYDIR);

            // Get all pictures directories
            $picturesDirs = glob(CAPTURES_DIR . '/*/*/pictures', GLOB_ONLYDIR);

            // Merge movies and pictures directories
            $mediasDirs = array_merge($moviesDirs, $picturesDirs);

            // Get all day directories
            $dirs = glob(CAPTURES_DIR . '/*/*', GLOB_ONLYDIR);

            // Delete event files older than retention period
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (filemtime($file) < strtotime('-' . MOTION_EVENTS_RETENTION . ' days')) {
                        if (!unlink($file)) {
                            throw new Exception('Could not delete file ' . $file);
                        }

                        parent::log($file . ' deleted');
                    }
                }
            }

            // Remove empty directories (movies and pictures directories)
            if (!empty($mediasDirs)) {
                foreach ($mediasDirs as $dir) {
                    if (Directory::isEmpty($dir)) {
                        if (!rmdir($dir)) {
                            throw new Exception('Could not delete empty directory ' . $dir);
                        }

                        parent::log('Directory ' . $dir . ' deleted');
                    }
                }
            }

            // Remove empty directories (day directories)
            if (!empty($dirs)) {
                foreach ($dirs as $dir) {
                    if (Directory::isEmpty($dir)) {
                        if (!rmdir($dir)) {
                            throw new Exception('Could not delete empty directory ' . $dir);
                        }

                        parent::log('Directory ' . $dir . ' deleted');
                    }
                }
            }
        }

        parent::log('Files cleaning finished');

        unset($dirs, $dir, $files, $file);
    }
}
