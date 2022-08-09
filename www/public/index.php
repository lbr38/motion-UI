<!DOCTYPE html>
<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');

\Controllers\Autoloader::load();

$mymotion = new \Controllers\Motion();

/**
 *  Démarrage ou arrêt de motion
 */
if (!empty($_GET['motion'])) {
    $mymotion = new \Controllers\Motion();
    $mymotion->stopStart($_GET['motion']);
}
?>

<?php include_once('../includes/head.inc.php'); ?>

<body>
    <?php
    /**
     *  Affichage des horaires actuelles directement dans le formulaire (pour faire un formulaire pré-rempli et modifiable)
     */
    $alertEnabled = $mymotion->getAlertStatus();

    /**
     *  Récupération de la configuration de l'autostart et des alertes
     */
    $alertConfiguration = $mymotion->getAlertConfiguration();
    $motionStatus = $mymotion->getStatus();
    $motionAutostartEnabled = $mymotion->getAutostartStatus();
    $autostartConfiguration = $mymotion->getAutostartConfiguration();
    $autostartDevicePresenceEnabled = $mymotion->getAutostartOnDevicePresenceStatus();
    $autostartKnownDevices = $mymotion->getAutostartDevices();
    ?>

    <div id="motionui-status">
        <?php
        /**
         *  Display a warning if motionUI service is not running
         */
        if ($mymotion->getMotionUIServiceStatus() != 'active') {
            echo '<p class="center yellowtext"><img src="resources/icons/warning.png" class="icon" /><b>motionui</b> service is not started. You must start it.</p>';
        } ?>
    </div>

    <div class="container">
        <div class="item">
            <h2>Live</h2>
            <a href="<?= '/live.php' ?>">
                <button class="btn-square-blue"><img src="resources/icons/camera.png" class="icon" /></button>
            </a>
            <span class="block center lowopacity">Visualize</span>
        </div>

        <div id="motion-start-div" class="item">
            <h2>Motion</h2>

            <?php
            if ($motionStatus != 'active') : ?>
                <button id="start-motion-btn" class="btn-square-green" title="Start motion service now">
                    <img src="resources/icons/power.png" class="icon" />
                </button>
                <span class="block center lowopacity">Start capture</span>
                
                <?php
            endif;

            if ($motionStatus == 'active') {
                echo '<button id="stop-motion-btn" class="btn-square-red" title="Stop motion service"><img src="resources/icons/power.png" class="icon" /></button>';
                echo '<span class="block center lowopacity">Stop capture</span>';
            } ?>
        </div>

        <div id="motion-autostart-container" class="item">
            <h2>Motion: autostart</h2>

            <?php
            if ($motionAutostartEnabled == "disabled") : ?>
                <button id="enable-autostart-btn" class="btn-square-green" title="Enable motion service autostart">
                    <img src="resources/icons/time.png" class="icon" />
                </button>
                <span class="block center lowopacity">Enable and configure autostart</span>
                <?php
            endif;
            if ($motionAutostartEnabled == "enabled") : ?>
                <button id="disable-autostart-btn" class="btn-square-red" title="Disable motion service autostart">
                    <img src="resources/icons/time.png" class="icon" />
                </button>
                <span class="block center lowopacity">Disable autostart</span>

                <?php
            endif ?>

            <br>

            <div id="autostart-btn-div">
                <?php
                if ($motionAutostartEnabled == "enabled") : ?>
                    <span id="configure-autostart-btn" class="btn-medium-blue">Configure autostart</span>
                    <?php
                endif ?>
            </div>

            <?php
            if ($motionAutostartEnabled == "enabled") : ?>
                <div id="autostart-div" class="config-div hide">
                    <p class="center">Configure autostart time slots:</p>

                    <br>

                    <form id="autostart-conf-form" autocomplete="off">
                        <table id="motion-alert-table">
                            <tr>
                                <th class="td-30"></th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                            <tr>
                                <th class="td-30">Monday</th>
                                <td><input type="time" name="monday-start" value="<?= $autostartConfiguration['Monday_start'] ?>" /></td>
                                <td><input type="time" name="monday-end" value="<?= $autostartConfiguration['Monday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Tuesday</th>
                                <td><input type="time" name="tuesday-start" value="<?= $autostartConfiguration['Tuesday_start'] ?>" /></td>
                                <td><input type="time" name="tuesday-end" value="<?= $autostartConfiguration['Tuesday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Wednesday</th>
                                <td><input type="time" name="wednesday-start" value="<?= $autostartConfiguration['Wednesday_start'] ?>" /></td>
                                <td><input type="time" name="wednesday-end" value="<?= $autostartConfiguration['Wednesday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Thursday</th>
                                <td><input type="time" name="thursday-start" value="<?= $autostartConfiguration['Thursday_start'] ?>" /></td>
                                <td><input type="time" name="thursday-end" value="<?= $autostartConfiguration['Thursday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Friday</th>
                                <td><input type="time" name="friday-start" value="<?= $autostartConfiguration['Friday_start'] ?>" /></td>
                                <td><input type="time" name="friday-end" value="<?= $autostartConfiguration['Friday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Saturday</th>
                                <td><input type="time" name="saturday-start" value="<?= $autostartConfiguration['Saturday_start'] ?>" /></td>
                                <td><input type="time" name="saturday-end" value="<?= $autostartConfiguration['Saturday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-30">Sunday</th>
                                <td><input type="time" name="sunday-start" value="<?= $autostartConfiguration['Sunday_start'] ?>" /></td>
                                <td><input type="time" name="sunday-end" value="<?= $autostartConfiguration['Sunday_end'] ?>" /></td>
                            </tr>

                            <?php
                            if ($autostartDevicePresenceEnabled == 'enabled') {
                                echo '<tr><td colspan="100%"><p class="yellowtext">Info: Autostart and stop on device presence is enabled. It will overwrite this configuration.</p></td></tr>';
                            }
                            ?>
                        </table>
                        <br>
                        <button type="submit" class="btn-small-blue">Save</button>
                    </form>

                    <br>
                    
                    <span>Enable autostart on device presence on the local network </span>
                    <label class="onoff-switch-label">
                        <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
                        <span class="onoff-switch-slider"></span>
                    </label>
                    
                    <?php
                    if ($autostartDevicePresenceEnabled == 'enabled') : ?>
                        <br>

                        <p>
                        - Motion will be started if none of the configured devices are present on the local network.<br>
                        - Motion will be stopped if at least <b>1</b> of the configured devices is connected to the local network.
                        </p>

                        <?php
                        if (!empty($autostartKnownDevices)) : ?>
                            <br>
                            <p><b>Known devices</b></p>

                            <table>
                                <?php
                                foreach ($autostartKnownDevices as $knownDevice) {
                                    $deviceId = $knownDevice['Id'];
                                    $deviceName = $knownDevice['Name'];
                                    $deviceIp = $knownDevice['Ip'];
                                    echo '<tr class="td-fit"><td><b>' . $deviceName . '</b></td><td>' . $deviceIp . '</td><td><img src="resources/icons/bin.png" class="icon-lowopacity remove-device-btn" device-id="' . $deviceId . '" title="Remove device ' . $deviceName . '" /></td></tr>';
                                } ?>
                            </table>
                            <hr>
                        <?php endif ?>

                        <br>

                        <form id="device-presence-form" autocomplete="off">
                            <p>Add a new device:</p>
                            <input type="text" name="device-name" placeholder="Device name" required />
                            <input type="text" name="device-ip" placeholder="IP address" required />

                            <br><br>
                            <button type="submit" class="btn-small-blue">Add device</button>
                        </form>
                    <?php endif ?>
                </div>

                <?php
            endif ?>
        </div>

        <div id="motion-alert-container" class="item">
            <h2>Motion: alerts</h2>

            <?php
            if ($alertEnabled == "disabled") {
                echo '<button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts"><img src="resources/icons/alarm.png" class="icon"></button>';
                echo '<span class="block center lowopacity">Enable and configure alerts</span>';
            }
            if ($alertEnabled == "enabled") {
                echo '<button type="button" id="disable-alert-btn" class="btn-square-red" title="Disable motion alerts"><img src="resources/icons/alarm.png" class="icon"></button>';
                echo '<span class="block center lowopacity">Disable alerts</span>';
            } ?>

            <br>

            <div id="alert-btn-div">
                <?php
                if ($alertEnabled == "enabled") {
                    echo '<span id="configure-alerts-btn" class="btn-small-blue">Configure alerts</span>';
                    echo '<button type="button" id="how-to-alert-btn" class="btn-medium-yellow">How to</button>';
                } ?>
            </div>

            <?php
            if ($alertEnabled == "enabled") : ?>
                <div id="alert-div" class="config-div hide">
                    <p class="center">Configure alerts time slots:</p>

                    <br>

                    <form id="alert-conf-form" autocomplete="off">
                        <table id="motion-alert-table">
                            <tr>
                                <th class="td-30"></th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                            <tr>
                                <th class="td-10">Monday</th>
                                <td><input type="time" name="monday-start" value="<?= $alertConfiguration['Monday_start'] ?>" /></td>
                                <td><input type="time" name="monday-end" value="<?= $alertConfiguration['Monday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Tuesday</th>
                                <td><input type="time" name="tuesday-start" value="<?= $alertConfiguration['Tuesday_start'] ?>" /></td>
                                <td><input type="time" name="tuesday-end" value="<?= $alertConfiguration['Tuesday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Wednesday</th>
                                <td><input type="time" name="wednesday-start" value="<?= $alertConfiguration['Wednesday_start'] ?>" /></td>
                                <td><input type="time" name="wednesday-end" value="<?= $alertConfiguration['Wednesday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Thursday</th>
                                <td><input type="time" name="thursday-start" value="<?= $alertConfiguration['Thursday_start'] ?>" /></td>
                                <td><input type="time" name="thursday-end" value="<?= $alertConfiguration['Thursday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Friday</th>
                                <td><input type="time" name="friday-start" value="<?= $alertConfiguration['Friday_start'] ?>" /></td>
                                <td><input type="time" name="friday-end" value="<?= $alertConfiguration['Friday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Saturday</th>
                                <td><input type="time" name="saturday-start" value="<?= $alertConfiguration['Saturday_start'] ?>" /></td>
                                <td><input type="time" name="saturday-end" value="<?= $alertConfiguration['Saturday_end'] ?>" /></td>
                            </tr>
                            <tr>
                                <th class="td-10">Sunday</th>
                                <td><input type="time" name="sunday-start" value="<?= $alertConfiguration['Sunday_start'] ?>" /></td>
                                <td><input type="time" name="sunday-end" value="<?= $alertConfiguration['Sunday_end'] ?>" /></td>
                            </tr>
                            <tr title="Specify mail alert recipient, separated by a comma.">
                                <th class="td-10">Mail recipient</th>
                                <td colspan="2"><input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" required />
                            </tr>
                            <tr title="(optional) Specify mutt configuration file path to use.">
                                <th class="td-10">Mutt config path</th>
                                <td colspan="2"><input type="text" name="mutt-config" value="<?= $alertConfiguration['Mutt_config'] ?>" />
                            </tr>

                            <?php
                            /**
                             *  Display a warning if event script does not exists
                             */
                            if (!file_exists(DATA_DIR . '/tools/event')) {
                                echo '<tr><td colspan="100%"><p class="yellowtext">The event script <b>' . DATA_DIR . '/tools/event</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                            }

                            /**
                             *  Display a warning if mutt config file is not readable or not exist
                             */
                            if (!empty($alertConfiguration['Mutt_config'])) {
                                if (!file_exists($alertConfiguration['Mutt_config'])) {
                                    echo '<tr><td colspan="100%"><p class="yellowtext">The specified mutt config file <b>' . $alertConfiguration['Mutt_config'] . '</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                                }

                                if (!is_readable($alertConfiguration['Mutt_config'])) {
                                    echo '<tr><td colspan="100%"><p class="yellowtext">The specified mutt config file <b>' . $alertConfiguration['Mutt_config'] . '</b> is not readable. You may configure alerts but you will not receive them.</p></td></tr>';
                                }
                            }
                            ?>
                        </table>

                        <br>
                        <button type="submit" class="btn-small-blue">Save</button>
                    
                        <br>
                    </form>
                </div>
                <?php
            endif ?>
        </div>
    </div>

    <div id="how-to-alert-container" class="config-div hide">
        <?php include_once('../includes/how-to-alert.php'); ?>
    </div>

    <hr>

    <div id="motion-configuration-div">

        <h2>Motion: configuration</h2>

        <div id="configuration-container">
            <?php
            $configurationFiles = glob('/etc/motion/*.conf');

            /**
             *  Set the main configuration file as first member of the array, to be displayed first
             */
            $configurationFiles = \Controllers\Common::arrayReorder($configurationFiles, '/etc/motion/motion.conf');

            if (!empty($configurationFiles)) :
                foreach ($configurationFiles as $configurationFile) :
                    /**
                     *  Keep only the filename (and not the entire path)
                     */
                    $configurationFile = basename($configurationFile); ?>

                    <div>
                        <div class="center">
                            <input type="text" class="input-medium center rename-motion-conf-input" filename="<?= $configurationFile ?>" placeholder="Rename <?= $configurationFile ?>" value="<?= $configurationFile ?>">

                            <?php
                            if ($configurationFile == 'motion.conf') {
                                echo '<br><span class="lowopacity">(main configuration file)</span>';
                            }
                            ?>

                            <?php
                            /**
                             *  Check that config file is readable and writable
                             */
                            if (!is_readable('/etc/motion/' . $configurationFile)) {
                                echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File not readable</span>';
                            }
                            if (!is_writable('/etc/motion/' . $configurationFile)) {
                                echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File not writable</span>';
                            }
                            ?>

                            <br><br>

                            <span class="btn-small-yellow show-motion-conf-btn" filename="<?= $configurationFile ?>">Show</span>
                            <span class="btn-small-blue duplicate-motion-conf-btn" filename="<?= $configurationFile ?>">Duplicate</span>
                            <span class="btn-xsmall-red delete-motion-conf-btn" filename="<?= $configurationFile ?>">Delete</span>
                        </div>

                        <br>

                        <div class="config-div hide" filename="<?= $configurationFile ?>">
                            <form class="motion-configuration-form" filename="<?= $configurationFile ?>" autocomplete="off">
                                <table class="motion-configuration-table">
                                    <tr>
                                        <th>E / D</th>
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
                    </div>
                    <?php
                endforeach;
            endif; ?>
        </div>
    </div>

    <?php include_once('../includes/footer.inc.php'); ?>
</body>
</html>