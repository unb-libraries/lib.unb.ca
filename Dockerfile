FROM ghcr.io/unb-libraries/drupal:9.x-4.x-unblib

ENV ADDITIONAL_OS_PACKAGES="postfix php82-ldap php82-xmlreader php82-zip php82-pecl-redis php82-bcmath openssh-client"
ENV DRUPAL_PRIVATE_FILE_PATH="/app/private_filesystem"
ENV DRUPAL_SITE_ID="libweb"
ENV DRUPAL_SITE_URI="lib.unb.ca"
ENV DRUPAL_SITE_UUID="87d22fc3-a2d0-4543-aab8-6ed800691b7b"

# Build application.
COPY ./build/ /build/
RUN ${RSYNC_MOVE} /build/scripts/container/ /scripts/ && \
  ${RSYNC_MOVE} /build/keys/ /app/keys/ && \
  /scripts/addOsPackages.sh && \
  /scripts/initOpenLdap.sh && \
  /scripts/setupStandardConf.sh && \
  /scripts/build.sh && \
  /scripts/linkDrupalCronEntryInitLib.sh

# Deploy configuration.
COPY ./configuration ${DRUPAL_CONFIGURATION_DIR}
RUN /scripts/pre-init.d/72_secure_config_sync_dir.sh

# Deploy custom modules, themes.
COPY ./custom/themes ${DRUPAL_ROOT}/themes/custom
COPY ./custom/modules ${DRUPAL_ROOT}/modules/custom

# Container metadata.
ARG BUILD_DATE
ARG VCS_REF
ARG VERSION
LABEL ca.unb.lib.generator="drupal9" \
  org.opencontainers.image.title="lib.unb.ca" \
  org.opencontainers.image.description="lib.unb.ca is the core web application at UNB Libraries." \
  org.opencontainers.image.vendor="University of New Brunswick Libraries" \
  org.opencontainers.image.authors="UNB Libraries <libsupport@unb.ca>" \
  org.opencontainers.image.url="https://lib.unb.ca" \
  org.opencontainers.image.source="https://github.com/unb-libraries/lib.unb.ca" \
  org.opencontainers.image.version="$VERSION" \
  org.opencontainers.image.revision="$VCS_REF" \
  org.opencontainers.image.created="$BUILD_DATE"
