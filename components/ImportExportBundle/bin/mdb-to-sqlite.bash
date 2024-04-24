#!/usr/bin/env bash

mdb_path=$1
sqlite_path=$2
id=$BASHPID
tmp_dir="/tmp/mdb-convert/${id}"

test -f $sqlite_path && rm $sqlite_path

echo "Starting conversion in ${tmp_dir}"
mkdir -p $tmp_dir
mdb-schema $mdb_path sqlite > ${tmp_dir}/schema.sql
mkdir -p ${tmp_dir}/sql
for i in $( mdb-tables $mdb_path ); do mdb-export -D "%Y-%m-%d %H:%M:%S" -H -I sqlite $mdb_path $i > ${tmp_dir}/sql/$i.sql; done
cat ${tmp_dir}/schema.sql | sqlite3 $sqlite_path
for f in ${tmp_dir}/sql/* ; do (echo 'BEGIN;'; cat $f; echo 'COMMIT;') | sqlite3 $sqlite_path; done
rm -rf $tmp_dir
