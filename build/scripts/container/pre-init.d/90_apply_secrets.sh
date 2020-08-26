#!/usr/bin/env sh

# OCLC API
OCLC_API_WSKEY=$(echo "$OCLC_API_WSKEY" | sed 's/\//\\\//g')
OCLC_API_SECRET=$(echo "$OCLC_API_SECRET" | sed 's/\//\\\//g')
OCLC_API_IDM_WSKEY=$(echo "$OCLC_API_IDM_WSKEY" | sed 's/\//\\\//g')
OCLC_API_IDM_SECRET=$(echo "$OCLC_API_IDM_SECRET" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/html/sites/all/settings/settings.oclc-api.inc'
sed -i "s|OCLC_API_WSKEY|$OCLC_API_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_SECRET|$OCLC_API_SECRET|g" $SETTINGS_FILE
sed -i "s|OCLC_API_IDM_WSKEY|$OCLC_API_IDM_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_IDM_SECRET|$OCLC_API_IDM_SECRET|g" $SETTINGS_FILE
