#!/usr/bin/env bash
. ${SCRIPS_DIR}/functions.fnsh
cd ${PROJECT_ROOT}
echo $PWD
f_title "Static Analyzer"
if [ -z "$1" ]; then
    SRC="src/"
else
    SRC="$1"
fi
$PHP ./bin/php-cs-fixer fix --config=.cs/.php_cs.php
$PHP ./bin/phpmd $SRC text .cs/md_ruleset.xml | tee -a ${DATADIR}/metrics/phpmd_results.md
$PHP ./bin/phpcpd $SRC | tee -a ${DATADIR}/metrics/phpcpd_results.md
$PHP ./bin/phpcs -n $SRC --standard=.cs/cs_ruleset.xml | grep -v "PHPCBF CAN FIX THE 4 MARKED SNIFF VIOLATIONS AUTOMATICALLY" | tee -a ${DATADIR}/metrics/phpcs_results.md
f_done
