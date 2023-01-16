<div id="alert-div" class="param-slide-container">
    <div class="param-slide alert-container">
        <img id="hide-alert-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />

        <h2 class="center">Configure alerts</h2>
        
        <p>Alerts are sent on event start and on video/picture save. Be sure that event registering is setted up in motion's configuration file(s).</p>

        <br>
        <h2>Alerts time period</h2>
        <br>

        <form id="alert-conf-form" autocomplete="off">
            <table class="config-table">
                <tr>
                    <th class="td-30"></th>
                    <th class="center">Start</th>
                    <th class="center">End</th>
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
                <tr title="Specify mail alert recipient(s), separated by a comma.">
                    <th class="td-10">Mail recipient</th>
                    <td colspan="2"><input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" required />
                </tr>

                <?php
                /**
                 *  Display a warning if event script does not exists
                 */
                if (!file_exists(DATA_DIR . '/tools/event')) {
                    echo '<tr><td colspan="100%"><p class="yellowtext">The event script <b>' . DATA_DIR . '/tools/event</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                }

                /**
                 *  Display a warning if mutt config file is missing
                 */
                if (!file_exists(DATA_DIR . '/.muttrc')) {
                    echo '<tr><td colspan="100%"><p class="yellowtext">Mutt config file <b>' . DATA_DIR . '/.muttrc</b> is not found. You may configure alerts but you will not receive them. Use the <b>Generate muttrc</b> button below to generate a new muttrc config file.</p></td></tr>';
                }

                /**
                 *  Display a warning if mutt config file is not readable
                 */
                if (file_exists(DATA_DIR . '/.muttrc')) {
                    if (!is_readable(DATA_DIR . '/.muttrc')) {
                        echo '<tr><td colspan="100%"><p class="yellowtext">The specified mutt config file <b>' . DATA_DIR . '/.muttrc</b> is not readable. You may configure alerts but you will not receive them.</p></td></tr>';
                    }
                } ?>
            </table>
            <br>

            <button type="submit" class="btn-small-green">Save</button>
            
            <?php
            if (!file_exists(DATA_DIR . '/.muttrc')) : ?>
                <button type="button" id="generate-muttrc-btn" class="btn-large-yellow" title="Generate muttrc config template">Generate muttrc config template</button>
                <?php
            endif; ?>
        </form>

        <?php
        if (file_exists(DATA_DIR . '/.muttrc')) :?>
            <br>
            <h2>Mail configuration</h2>
            
            <?php
            $emptyParam = 0;
            $realName = '';
            $from = '';
            $smtpUrl = '';
            $smtpPassword = '';

            /**
             *  Parse muttrc file
             */
            $muttIni = parse_ini_file(DATA_DIR . '/.muttrc');

            /**
             *  Check that each mutt parameter has a value
             */
            foreach ($muttIni as $muttParam => $muttParamValue) {
                if (empty($muttParamValue)) {
                    $emptyParam++;
                }
            }

            /**
             *  Get params values
             */
            if (!empty($muttIni['set realname'])) {
                $realName = $muttIni['set realname'];
            }
            if (!empty($muttIni['set from'])) {
                $from = $muttIni['set from'];
            }
            if (!empty($muttIni['set smtp_url'])) {
                $smtpUrl = $muttIni['set smtp_url'];
            }
            if (!empty($muttIni['set smtp_pass'])) {
                $smtpPassword = $muttIni['set smtp_pass'];
            }

            /**
             *  Print a message if one or more param has an empty value
             */
            if ($emptyParam != 0) {
                echo '<p class="yellowtext">Mutt configuration is incomplete. Please fill all the blank fields with a value or you may not receive alerts.</p>';
            } ?>

            <br>

            <p>Edit mutt configuration:</p>

            <form id="mutt-config-form" autocomplete="off">
                <table>
                    <tr>
                        <td>
                            Real name
                        </td>
                        <td>
                            <input type="text" name="realname" value="<?= $realName ?>" required />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Sender mail
                        </td>
                        <td>
                            <input type="email" name="from" value="<?= $from ?>" required />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Sender mail password
                        </td>
                        <td>
                            <input type="password" name="smtp-password" value="<?= $smtpPassword ?>" required />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            SMTP URL
                        </td>
                        <td>
                            <input type="text" name="smtp-url" value="<?= $smtpUrl ?>" placeholder="smtps://SENDER@MAIL.COM@smtp.gmail.com:465/" required />
                        </td>
                    </tr>
                </table>

                <br>
                <button type="submit" class="btn-small-green">Save</button>
            </form>
            <?php
        endif ?>
        <br>
        <br>
    </div>
</div>