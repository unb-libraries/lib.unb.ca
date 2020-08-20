FROM unblibraries/drupal:8.x-2.x-slim-unblib
MAINTAINER UNB Libraries <libsupport@unb.ca>

ENV DRUPAL_SITE_ID libweb
ENV DRUPAL_SITE_URI lib.unb.ca
ENV DRUPAL_SITE_UUID 87d22fc3-a2d0-4543-aab8-6ed800691b7b

# Override upstream scripts with those from this repository.
COPY ./scripts/container /scripts

# Install additional OS packages.
ENV ADDITIONAL_OS_PACKAGES rsyslog postfix php7-ldap php7-xmlreader php7-zip php7-redis
RUN /scripts/addOsPackages.sh && \
  echo "TLS_REQCERT never" > /etc/openldap/ldap.conf && \
  /scripts/initRsyslog.sh

# Add package configuration, build webtree.
COPY ./package-conf /package-conf
RUN /scripts/setupStandardConf.sh
COPY ./build /build
RUN /scripts/build.sh ${DRUPAL_BASE_PROFILE}

# Deploy repository assets.
COPY ./config-yml ${DRUPAL_CONFIGURATION_DIR}
COPY ./custom/themes ${DRUPAL_ROOT}/themes/custom
COPY ./custom/modules ${DRUPAL_ROOT}/modules/custom

# Metadata.
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
