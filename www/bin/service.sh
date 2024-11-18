#!/bin/bash

# Run commands every day
while true; do
    /usr/bin/apt-get clean all > /dev/null
    /usr/bin/apt-get update -y -qq > /dev/null

    # Sleep for a day
    /usr/bin/sleep 86400
done