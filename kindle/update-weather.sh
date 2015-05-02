#!/bin/sh

# Adopted from https://github.com/mpetroff/kindle-weather-display
# First mntroot rw, then add following cronjob to /etc/crontab/root, then /etc/init.d/cron restart, finally mntroot ro
# 5,20,35,50 * * * * /bin/sh /mnt/base-us/kindle-weather-display/update-weather.sh > /dev/null 2>&1
# BTW, I choose to put all files on USB drive instead of system partition. Also the update is shorten to every 15 minutes with offset of 5 minutes earlier than server updates.

cd "$(dirname "$0")"
# Clear the display
eips -c
eips -c

# Check battery capacity
BATTERY=`grep -o "[0-9]*" /sys/devices/system/yoshi_battery/yoshi_battery0/battery_capacity`
CURRENT=`cat /sys/devices/system/yoshi_battery/yoshi_battery0/battery_current`

if [ $BATTERY -le 10 ] && [ $CURRENT -le 0 ]; then
	# Show battery drained image
    eips -g battery-drained.png
else
	# Stop services to turn Kindle into a display puppet :P
	/etc/init.d/framework stop
	/etc/init.d/powerd stop
	# Remove old file
	rm weather-update.png
	# Download updated weather picture
	if wget "http://Your-Website-Domain/weather-update.png"; then
		eips -g weather-update.png
	else
		eips -g weather-error.png
	fi
fi
