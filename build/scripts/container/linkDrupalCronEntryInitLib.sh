#!/usr/bin/env sh

# Apply environment based secrets to configuration.
ln -s /scripts/pre-init.d/90_apply_secrets.sh /scripts/pre-init.cron.d/
