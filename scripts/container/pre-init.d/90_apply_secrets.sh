#!/usr/bin/env sh

# OCLC API
FILE='/app/html/modules/custom/lib_core/src/Plugin/Block/LaptopAvailability.php'
sed -i "s|OCLC_API_WSKEY|$OCLC_API_WSKEY|g" $FILE
sed -i "s|OCLC_API_SECRET|$OCLC_API_SECRET|g" $FILE
