<p class="center">
    <b>How to edit motion's configuration files to send mail alerts</b><br><br>
<p>

<p>
    <b>Prerequisite</b><br><br>
</p>

<p>
    1. Set up a mail client:<br>
    - Install <b>mutt</b> package if not already installed.<br>
    - Create a new configuration file <b>/var/lib/motionui/.muttrc</b>. You can create it anywhere else but it should be readable by <b>motion</b> user.<br>
    - Insert your muttrc configuration (you can easily find exemples on the Internet) and check if motion can send a mail:<br>
</p>

<pre>sudo -u motion echo '' | mutt -s 'test' -F /var/lib/motionui/.muttrc myemail@mail.com</pre>
<br><br>
<p>
    2. Be sure that the <b>event</b> script is present in <b>/var/lib/motionui/tools/event</b>.<br>
    - This script will be used by motion to generate event file that will be processed by motionui systemd service.<br>
    - Ensure motion user can <b>execute</b> this script:
</p>

<pre>sudo chmod 550 /var/lib/motionui/tools/event<br>sudo chown motion:motionui /var/lib/motionui/tools/event<br><br>ls -l /var/lib/motionui/tools/event<br>-r-xr-x--- 1 motion motionui 5667 juil. 24 14:18 /var/lib/motionui/tools/event</pre>
<br><br>
<p>
    3. Be sure that <b>motionui</b> service is started and enabled on boot:
</p>

<pre>sudo systemctl start motionui<br>sudo systemctl enable motionui</pre>
<br><br>
<p>
    <b>Configuration</b><br><br>
</p>
<p>
    4. Configure motion to send alert on specific triggers. Edit your motion configuration file from the <b>Motion: configuration</b> section below and set the following triggers to execute the <b>event</b> script:<br>
    - Enable and edit this parameter to be sure to receive a mail alert on every new motion detection:
</p>

<pre>Parameter: on_event_start<br>Value: /var/lib/motionui/tools/event --cam-id %t --cam-name %$ --register-event %v</pre>
<p>
    - Enable and edit this parameter to be sure that the event end will be take into account:
</p>

<pre>Parameter: on_event_end<br>Value: /var/lib/motionui/tools/event --cam-id %t --end-event %v</pre>
<p>
    - (Opt.) Enable and edit this parameter to be sure to receive a mail with an attached video generated from the last detected motion:
</p>

<pre>Parameter: on_movie_end<br>Value : /var/lib/motionui/tools/event --cam-id %t --event %v --file %f</pre>
<p>
    - (Opt.) Enable and edit this parameter to be sure to receive a mail with attached JPEG pictures from the last detected motion (this parameter is not advised as you will receive a lot of mail):
</p>

<pre>Parameter: on_picture_save<br>Value: /var/lib/motionui/tools/event --cam-id %t --event %v --file %f</pre>
<p>
    - Save configuration and (re)start motion to apply.
</p>