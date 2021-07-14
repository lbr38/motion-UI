<!DOCTYPE html>
<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');

\Controllers\Autoloader::load();

$myalert = new \Controllers\Alert();
$mymotion = new \Controllers\Motion();

/**
 *  Démarrage ou arrêt de motion
 */
if (!empty($_GET['motion'])) {
    $mymotion = new \Controllers\Motion();
    $mymotion->stopStart($_GET['motion']);
}

/**
 *  Activation ou désactivation des alertes
 */
if (!empty($_GET['enablealert'])) {
    $myalert->enable($_GET['enablealert']);
}
?>

<?php include_once('../includes/head.inc.php'); ?>

<body>
    <?php

    /**
     *  Affichage des horaires actuelles directement dans le formulaire (pour faire un formulaire pré-rempli et modifiable)
     */
    $alertEnabled = $myalert->getStatus();

    /**
     *  Récupération de la configuration des alertes
     */
    $alertConfiguration = $myalert->getConfiguration();

    $monday = explode('-', $alertConfiguration['monday']);
    $tuesday = explode('-', $alertConfiguration['tuesday']);
    $wednesday = explode('-', $alertConfiguration['wednesday']);
    $thursday = explode('-', $alertConfiguration['thursday']);
    $friday = explode('-', $alertConfiguration['friday']);
    $saturday = explode('-', $alertConfiguration['saturday']);
    $sunday = explode('-', $alertConfiguration['sunday']);
    $mondayStart = $monday[0];
    $mondayEnd = $monday[1];
    $tuesdayStart = $tuesday[0];
    $tuesdayEnd = $tuesday[1];
    $wednesdayStart = $wednesday[0];
    $wednesdayEnd = $wednesday[1];
    $thursdayStart = $thursday[0];
    $thursdayEnd = $thursday[1];
    $fridayStart = $friday[0];
    $fridayEnd = $friday[1];
    $saturdayStart = $saturday[0];
    $saturdayEnd = $saturday[1];
    $sundayStart = $sunday[0];
    $sundayEnd = $sunday[1];
    ?>

    <div class="container">
        <div class="item">
            <h2>Live</h2>
            <a href="<?php echo "/live.php"; ?>"><button class="btn-square-green"><img src="ressources/images/camera.png" class="icon" /></button></a>
            <span class="block center lowopacity">Visualize</span>
        </div>

        <div id="motion-start-div" class="item">
            <h2>Motion</h2>

            <?php
            $motionStatus = $mymotion->getStatus();

            if (!empty($motionStatus == 'stopped')) {
                echo '<button id="start-motion-btn" class="btn-square-green" title="Start motion capture"><img src="ressources/images/power.png" class="icon" /></button>';
                echo '<span class="block center lowopacity">Start capture</span>';
            }
            if (!empty($motionStatus == 'started')) {
                echo '<button id="stop-motion-btn" class="btn-square-red" title="Stop motion capture"><img src="ressources/images/power.png" class="icon" /></button>';
                echo '<span class="block center lowopacity">Stop capture</span>';
            } ?>
        </div>

        <div id="motion-alert-div" class="item">
            <h2>Motion : alerts</h2>

            <?php
            if ($alertEnabled == "no") {
                echo '<button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts"><img src="ressources/images/alarm.png" class="icon"></button>';
                echo '<span class="lowopacity">Enable and configure alerts</span>';
            }

            if ($alertEnabled == "yes") : ?>
                <form id="alert-conf-form">
                    <table id="motion-alert-table">
                        <tr>
                            <th class="td-30"></th>
                            <th>Start</th>
                            <th>End</th>
                        </tr>
                        <tr>
                            <th class="td-30">Monday</th>
                            <td><input type="time" name="monday_start" value="<?= $mondayStart ?>" /></td>
                            <td><input type="time" name="monday_end" value="<?= $mondayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Tuesday</th>
                            <td><input type="time" name="tuesday_start" value="<?= $tuesdayStart ?>" /></td>
                            <td><input type="time" name="tuesday_end" value="<?= $tuesdayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Wednesday</th>
                            <td><input type="time" name="wednesday_start" value="<?= $wednesdayStart ?>" /></td>
                            <td><input type="time" name="wednesday_end" value="<?= $wednesdayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Thursday</th>
                            <td><input type="time" name="thursday_start" value="<?= $thursdayStart ?>" /></td>
                            <td><input type="time" name="thursday_end" value="<?= $thursdayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Friday</th>
                            <td><input type="time" name="friday_start" value="<?= $fridayStart ?>" /></td>
                            <td><input type="time" name="friday_end" value="<?= $fridayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Saturday</th>
                            <td><input type="time" name="saturday_start" value="<?= $saturdayStart ?>" /></td>
                            <td><input type="time" name="saturday_end" value="<?= $saturdayEnd ?>" /></td>
                        </tr>
                        <tr>
                            <th class="td-30">Sunday</th>
                            <td><input type="time" name="sunday_start" value="<?= $sundayStart ?>" /></td>
                            <td><input type="time" name="sunday_end" value="<?= $sundayEnd ?>" /></td>
                        </tr>
                        <?php
                        /**
                         *  Display a warning if send-alert.sh does not exists
                         */
                        if (!file_exists('/etc/motion/send-alert.sh')) {
                            echo '<tr><td colspan="100%"><p class="yellowtext">The bash script <b>/etc/motion/send-alert.sh</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                        }
                        ?>
                    </table>

                    <br>
                    <button type="submit" class="btn-small-blue">Save</button>
                    <button type="button" id="disable-alert-btn" class="btn-medium-red"><img src="ressources/images/alarm.png" class="icon">Disable alerts</button>
                    <button type="button" id="how-to-alert-btn" class="btn-medium-yellow">How to</button>
                
                    <br>

                    
                </form>
                <?php
            endif ?>
        </div>
    </div>

    <div id="how-to-alert-container" class="hide">
        <p>
            <b>How to send alerts</b><br><br>
            1. Set up a mail client.<br>
            - Install <b>mutt</b><br>
            - Create a new configuration file <b>/etc/motion/.muttrc</b>. You can create it anywhere else but it should be readable by motion user.<br>
            - Insert your mutt configuration and check if motion can send a mail:<br>
        </p>
        <pre>sudo -u motion echo '' | mutt -s 'test' -F /etc/motion/.muttrc myemail@mail.com</pre>
        <br>    
        <p>
            2. Copy <b>send-alert.sh</b> script to <b>/etc/motion/</b>.<br>
            - This script will be used to send alerts at the time sheduled in the above configuration.<br>
            - Ensure motion user can execute this script:
        </p>
        <pre>-rwx------   1 motion   motion  2420 juil. 21  2021 send-alert.sh</pre>
        <br>
        <p>
            3. Configure motion to send alert on a specific trigger.<br>
            The possible triggers are: <br>
             - on_event_start<br>
             - on_event_end<br>
             - on_picture_save<br>
             - on_motion_detected<br>
             - on_area_detected<br>
             - on_movie_start<br>
             - on_movie_end<br>
             - on_camera_lost<br>
            <br>
            Edit your motion configuration file below and set the desired trigger to execute send-alert.sh.<br>
            e.g.<br>
            - Send a mail on every new motion detection:
        </p>
        <pre>on_event_start sh /etc/motion/send-alert.sh -c /etc/motion/.muttrc -r myemail@mail.com -s 'Insert subject here, for example: a new motion has been detected'</pre>
        <p>
            - Then when motion has generated a video from the last detected motion, it places the video filename inside <b>%f</b> variable which can be used to send a new mail alert with the video attached this time:
        </p>
        <pre>on_movie_end sh /etc/motion/send-alert.sh -c /etc/motion/.muttrc -r myemail@mail.com -s 'Insert subject here, for example: video of the last detected motion, see attachment' -f %f</pre>
    </div>

    <hr>

    <div id="configuration-container">

        <h2>Motion : configuration</h2>

            <?php
            $configurationFiles = glob("/etc/motion/*.conf");

            /**
             *  Set the main configuration file as first member of the array, to be displayed first
             */
            $configurationFiles = \Controllers\Common::arrayReorder($configurationFiles, '/etc/motion/motion.conf');

            foreach ($configurationFiles as $configurationFile) :
                /**
                 *  Keep only the filename (and not the entire path)
                 */
                $configurationFile = basename($configurationFile);
                ?>

                <div class="center">
                    <span class="lowopacity">
                        <?= $configurationFile ?>
                        <?php
                        if (preg_match('/motion.conf/', $configurationFile)) {
                            echo ' (main configuration file)';
                        } ?>
                    </span>
                    <br><br>
                    <span class="btn-small-yellow show-motion-conf-btn" filename="<?= $configurationFile ?>">Show</span>
                </div>

                <br>

                <div class="hide" filename="<?= $configurationFile ?>">
                    <form class="motion-configuration-form" filename="<?= $configurationFile ?>" autocomplete="off">
                        <table id="motion-configuration-table">
                            <tr>
                                <th>Enabled</th>
                                <th>Parameter</th>
                                <th>Value</th>
                            </tr>

                            <?php
                            $content = file('/etc/motion/' . $configurationFile);
                            $content = str_replace('; ', ';', $content);

                            $i = 0;

                            foreach ($content as $line) :
                                /**
                                 *  Si la ligne est un commentaire alors on l'ignore
                                 */
                                if (preg_match('/^#/', $line)) {
                                    continue;
                                }

                                /**
                                 *  On parse la ligne pour séparer le parametre et sa valeur.
                                 *  Le parametre est alors placé en [0] et sa valeur (le reste de la ligne) en [1]
                                 */
                                $line = explode(' ', $line, 2);

                                /**
                                 *  Si la ligne est vide on passe à la suivante
                                 */
                                if (empty($line[0]) or empty($line[1])) {
                                    continue;
                                }

                                if (!empty($line[0])) {
                                    $optionName = $line[0];
                                } else {
                                    $optionName = '';
                                }

                                if (isset($line[1]) and $line[1] != "") {
                                    $optionValue = $line[1];
                                } else {
                                    $optionValue = '';
                                }

                                /**
                                 *  Si le paramètre commence par ';' alors celui-ci est désactivé
                                 *  On retire également le ';' dans l'affichage du paramètre
                                 */
                                if (preg_match('/^;/', $optionName)) {
                                    $status = 'disabled';
                                    $optionName = str_replace(';', '', $optionName);
                                } else {
                                    $status = 'enabled';
                                }

                                /**
                                 *  Si l'option contient un dièse # alors il s'agit d'un commentaire
                                 *  On l'ignore et on passe à la ligne suivante
                                 */
                                if (preg_match('/^#/', $optionName)) {
                                    continue;
                                }
                                ?>

                                <tr>
                                    <td class="td-fit">
                                        <label class="onoff-switch-label">
                                            <input class="onoff-switch-input" type="checkbox" name="option-status" option-id="<?= $i ?>" value="enabled" <?php echo ($status == 'enabled') ? 'checked' : ''?>>
                                            <span class="onoff-switch-slider"></span>
                                        </label>
                                    </td>
                                    <th class="td-10">
                                        <input type="hidden" name="option-name" option-id="<?= $i ?>" value="<?= $optionName ?>" />
                                        <?= $optionName ?>
                                    </th>
                                    <td>
                                        <input type="text" name="option-value" option-id="<?= $i ?>" value="<?= $optionValue ?>" />
                                    </td>
                                </tr>

                                <?php
                                ++$i;
                            endforeach ?>
                        </table>

                        <br>
                        <button type="submit" class="btn-small-blue">Save</button>
                        <button type="button" class="btn-small-yellow hide-motion-conf-btn" filename="<?= $configurationFile ?>">Hide</button>
                    </form>
                </div>
                <?php
            endforeach ?>
    </div>

    <?php include_once('../includes/footer.inc.php'); ?>
</body>
</html>