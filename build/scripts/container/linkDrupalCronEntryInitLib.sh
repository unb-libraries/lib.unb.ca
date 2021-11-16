#!/usr/bin/env sh

# Setup Private Filesystem.
ln -s /scripts/pre-init.d/80_set_private_filesystem.sh /scripts/pre-init.cron.d/

# Apply environment based secrets to configuration.
ln -s /scripts/pre-init.d/90_apply_secrets.sh /scripts/pre-init.cron.d/
