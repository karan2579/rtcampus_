#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.
#!/bin/bash

# Get the absolute path to cron.php
CRON_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/cron.php"

# Create a log file path
LOG_FILE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/cron.log"

# CRON line
CRON_JOB="0 9 * * * php $CRON_PATH >> $LOG_FILE 2>&1"

# Add the CRON job if it's not already added
(crontab -l 2>/dev/null | grep -Fv "$CRON_PATH" ; echo "$CRON_JOB") | crontab -

echo "✅ CRON job set to run daily at 9 AM and log to cron.log"
