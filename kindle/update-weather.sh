#!/bin/sh

# Adopted from https://github.com/mpetroff/kindle-weather-display
# First mntroot rw, then add following cronjob to /etc/crontab/root, then /etc/init.d/cron restart, finally mntroot ro
# 5,20,35,50 * * * * /bin/sh /mnt/base-us/kindle-weather-display/update-weather.sh > /dev/null 2>&1
# BTW, I choose to put all files on USB drive instead of system partition. Also the update is shorten to every 15 minutes with offset of 5 minutes earlier than server updates.

# Stop services to turn Kindle into a display puppet :P
/etc/init.d/framework stop
/etc/init.d/powerd stop

cd "$(dirname "$0")"

# Remove old file
rm weather-update.png
# Clear the display
eips -c
eips -c

# Download updated weather picture
if wget "http://Your-Website-Domain/weather-update.png"; then
	eips -g weather-update.png
else
	# Here comes the sad face
	eips -g weather-error.png
fi
