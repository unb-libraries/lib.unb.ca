#!/usr/bin/env sh

# OCLC Key
OCLC_API_WSKEY=$(echo "$OCLC_API_WSKEY" | sed 's/\//\\\//g')
OCLC_API_SECRET=$(echo "$OCLC_API_SECRET" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/keys/api.oclc.json'
sed -i "s|OCLC_API_WSKEY|$OCLC_API_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_SECRET|$OCLC_API_SECRET|g" $SETTINGS_FILE

# OCLC IDM Key
OCLC_API_IDM_WSKEY=$(echo "$OCLC_API_IDM_WSKEY" | sed 's/\//\\\//g')
OCLC_API_IDM_SECRET=$(echo "$OCLC_API_IDM_SECRET" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/keys/api.oclc-idm.json'
sed -i "s|OCLC_API_IDM_WSKEY|$OCLC_API_IDM_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_IDM_SECRET|$OCLC_API_IDM_SECRET|g" $SETTINGS_FILE

# OCLC LMAN Key
OCLC_API_LMAN_WSKEY=$(echo "$OCLC_API_LMAN_WSKEY" | sed 's/\//\\\//g')
OCLC_API_LMAN_SECRET=$(echo "$OCLC_API_LMAN_SECRET" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/keys/api.oclc-lman.json'
sed -i "s|OCLC_API_LMAN_WSKEY|$OCLC_API_LMAN_WSKEY|g" $SETTINGS_FILE
sed -i "s|OCLC_API_LMAN_SECRET|$OCLC_API_LMAN_SECRET|g" $SETTINGS_FILE

# OCLC KB Key
OCLC_API_KB=$(echo "$OCLC_API_KB" | sed 's/\//\\\//g')
SETTINGS_FILE='/app/keys/api.knowledge_base.json'
sed -i "s|OCLC_API_KB|$OCLC_API_KB|g" $SETTINGS_FILE

# FogBugz Credentials
SETTINGS_FILE='/app/keys/api.fogbugz.json'
sed -i "s|FOGBUGZ_USERNAME|$FOGBUGZ_USERNAME|g" $SETTINGS_FILE
sed -i "s|FOGBUGZ_PASSWORD|$FOGBUGZ_PASSWORD|g" $SETTINGS_FILE

cp /app/keys/oclc-sftp.root.key /app/keys/oclc-sftp.key
chown nginx /app/keys/oclc-sftp.key
