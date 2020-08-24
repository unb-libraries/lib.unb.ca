#!/usr/bin/env sh

# OCLC API
OCLC_API_WSKEY=$(echo "$OCLC_API_WSKEY" | sed 's/\//\\\//g')
OCLC_API_SECRET=$(echo "$OCLC_API_SECRET" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/html/sites/all/settings/settings.oclc-api.inc'
sed -i "s|OCLC_API_WSKEY|$OCLC_API_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_SECRET|$OCLC_API_SECRET|g" $SETTINGS_FILE

# Patron DB
SETTINGS_FILE='/app/html/sites/all/settings/settings.patrondb.inc'
sed -i "s|PATRON_DATABASE_USER|$PATRON_DATABASE_USER|g" $SETTINGS_FILE
sed -i "s|PATRON_DATABASE_PASSWORD|$PATRON_DATABASE_PASSWORD|g" $SETTINGS_FILE
sed -i "s|PATRON_DATABASE_HOST|$PATRON_DATABASE_HOST|g" $SETTINGS_FILE
sed -i "s|PATRON_DATABASE|$PATRON_DATABASE|g" $SETTINGS_FILE
