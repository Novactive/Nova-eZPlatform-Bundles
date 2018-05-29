#!/usr/bin/env bash
. ${SCRIPS_DIR}/functions.fnsh
cd ${PROJECT_ROOT}
f_title "Tests"
echo "Unit Tests - PHPUNIT"
$PHP ./bin/phpunit -c ./app
f_done
