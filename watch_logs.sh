#!/bin/bash
# Script to watch error logs in real-time during debugging
# Usage: ./watch_logs.sh

LOG_FILE="/home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log"

echo "=============================================="
echo "Watching error.log for variant listing debug"
echo "Press Ctrl+C to stop"
echo "=============================================="
echo ""

# Clear previous logs related to convertGroupToListing and saveListing
echo "Clearing previous debug logs..."
> "$LOG_FILE"
echo "Logs cleared. Ready to capture new debug output."
echo ""
echo "Now test the 'Save to Database' button in the admin panel."
echo ""
echo "Tailing log file..."
tail -f "$LOG_FILE"
