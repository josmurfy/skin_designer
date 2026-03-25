#!/bin/bash
# Script to view only variant listing debug logs
# Usage: ./view_listing_logs.sh

LOG_FILE="/home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log"

echo "=============================================="
echo "Variant Listing Creator Debug Logs"
echo "=============================================="
echo ""

# Show logs related to convertGroupToListing and saveListing
echo "=== convertGroupToListing Logs ==="
grep -A 50 "convertGroupToListing:" "$LOG_FILE" | tail -100
echo ""

echo "=== saveListing Logs ==="
grep -A 30 "saveListing:" "$LOG_FILE" | tail -100
echo ""

echo "=== Recent Errors (last 20 lines) ==="
tail -20 "$LOG_FILE"
