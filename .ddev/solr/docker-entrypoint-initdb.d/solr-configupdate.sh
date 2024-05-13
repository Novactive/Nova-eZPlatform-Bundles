#!/usr/bin/env bash
#ddev-generated
set -e

# Ensure "collection1" (or alternate SOLR_CORENAME) core config is always up to date even after the
# core has been created. This does not execute the first time,
# when solr-precreate has not yet run.
CORENAME=${SOLR_CORENAME:-collection1}
if [ -d /var/solr/data/${CORENAME}/conf ]; then
    cp /solr-conf/conf/*.xml /var/solr/data/${CORENAME}/conf/
fi