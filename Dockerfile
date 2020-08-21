FROM unblibraries/drupal:8.x-2.x-slim-unblib
MAINTAINER UNB Libraries <libsupport@unb.ca>

ENV ADDITIONAL_OS_PACKAGES rsyslog postfix php7-ldap php7-xmlreader php7-zip php7-redis
ENV DRUPAL_SITE_ID libweb
ENV DRUPAL_SITE_URI lib.unb.ca
ENV DRUPAL_SITE_UUID 87d22fc3-a2d0-4543-aab8-6ed800691b7b

# Build application.
COPY ./build /build
RUN ${RSYNC_MOVE} /build/scripts/container/ /scripts/ && \
  /scripts/addOsPackages.sh && \
  /scripts/initOpenLdap.sh && \
  /scripts/initRsyslog.sh && \
  /scripts/setupStandardConf.sh && \
  /scripts/build.sh

# Deploy custom assets, configuration.
COPY ./config-yml ${DRUPAL_CONFIGURATION_DIR}
COPY ./custom/themes ${DRUPAL_ROOT}/themes/custom
COPY ./custom/modules ${DRUPAL_ROOT}/modules/custom

# Container metadata.
ARG BUILD_DATE
ARG VCS_REF
ARG VERSION
LABEL ca.unb.lib.generator="drupal8" \
  com.microscaling.docker.dockerfile="/Dockerfile" \
  com.microscaling.license="MIT" \
  org.label-schema.build-date=$BUILD_DATE \
  org.label-schema.description="lib.unb.ca is the core web application at UNB Libraries." \
  org.label-schema.name="lib.unb.ca" \
  org.label-schema.schema-version="1.0" \
  org.label-schema.url="https://lib.unb.ca" \
  org.label-schema.vcs-ref=$VCS_REF \
  org.label-schema.vcs-url="https://github.com/unb-libraries/lib.unb.ca" \
  org.label-schema.vendor="University of New Brunswick Libraries" \
  org.label-schema.version=$VERSION
