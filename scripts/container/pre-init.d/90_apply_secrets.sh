#!/usr/bin/env sh

# OCLC API
SETTINGS_FILE='/app/html/sites/all/settings/settings.oclc-api.inc'
sed -i "s|OCLC_API_WSKEY|$OCLC_API_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_SECRET|$OCLC_API_SECRET|g" $SETTINGS_FILE
