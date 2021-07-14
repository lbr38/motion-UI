#!/bin/bash
  
CONF="/etc/motion/alert.ini"
DAY=$(LC_ALL="en_EN.UTF-8" date +%A)
TIME=$(date +%H%M)
# mutt params
MUTT_CMD="mutt"
MUTT_CONF=""
# mail alert params
MAIL_RECPT=""
# default subject
MAIL_SUBJECT="[ Motion ] - alert"
MAIL_MESSAGE=""
MAIL_FILE=""

help() {
echo "Parameters :
 mutt :
  -c  --mutt-conf       Configuration file to use for mutt

 mail alert :
  -r  --recipient       Recipient mail, you can precise more than one recipient by using ','
  -s  --subject         Mail subject (default = '[ Motion ] - alert')
  -m  --message         Mail message
  -f  --file            Mail attached file (motion places last recorded file in %f, so you better use this parameter in motion conf like : -f %f)
"
}

while [ $# -ge 1 ];do
    case "$1" in
        -h|--help)
            help
            exit
        ;;
        -c|--mutt-conf)
            MUTT_CONF="$2"
            shift
        ;;
        -r|--recipient)
            MAIL_RECPT="$2"
            shift
        ;;
        -s|--subject)
            MAIL_SUBJECT="$2"
            shift
        ;;
        -m|--message)
            MAIL_MESSAGE="$2"
            shift
        ;;
        -f|--file)
            MAIL_FILE="$2"
            shift
        ;;
           *)
            echo "Unknown parameter : $1"
        help
        exit
           ;;
        esac
    shift
done

if [ -z "$MAIL_RECPT" ];then
    echo "Error : no recipient specified"
    exit
fi

if ! grep -qi "$DAY" $CONF;then
    echo "Error : cannot find day $DAY in alerts.ini file"
    exit
fi

if ! grep -q "alert_enable" $CONF;then
    echo "Error : cannot find 'alert_enable' parameter in alerts.ini file"
    exit
fi

if grep -q 'alert_enable = "no"' $CONF;then
    echo "Alerts are disabled"
    exit
fi

for TIMES in $(grep -i "$DAY" $CONF | awk -F'=' '{print $2}');do
    TIME_START=$(echo $TIMES | awk -F'-' '{print $1}' | sed 's/://g' | sed 's/"//g' | sed 's/\r//g')
    TIME_END=$(echo $TIMES | awk -F'-' '{print $2}' | sed 's/://g' | sed 's/"//g' | sed 's/\r//g')

    echo "Time : $TIME"
    echo "Alert time start : $TIME_START"
    echo "Alert time end   : $TIME_END"

    if [ "$TIME" -gt "$TIME_START" ] && [ "$TIME" -lt "$TIME_END" ];then
        # Building final mutt command

        # use conf file
        if [ ! -z "$MUTT_CONF" ];then
            MUTT_CMD="mutt -F '$MUTT_CONF'"
        fi

        # add mail subject
        MUTT_CMD="$MUTT_CMD -s '$MAIL_SUBJECT'"

        # add attached file
        if [ ! -z "$MAIL_FILE" ];then
            MUTT_CMD="$MUTT_CMD -a '$MAIL_FILE'"
        fi

        # add recipient to final command
        MUTT_CMD="$MUTT_CMD -- '$MAIL_RECPT'"

        eval "$MUTT_CMD"
        fi
done

exit