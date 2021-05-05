#!/bin/bash

mkdir -p ../tmp

directories=$(find ../deploy -depth -type d | cut -sd / -f3-)


for dir in $directories; do
        echo "-rm project_test/${dir}/*" >> ../tmp/delete.txt
        echo "-rmdir project_test/${dir}" >> ../tmp/delete.txt
done

echo "-rm project_test/*" >> ../tmp/delete.txt
echo "-rmdir project_test" >> ../tmp/delete.txt

sshpass -e sftp -P 20022 -oBatchMode=no -b ../tmp/delete.txt andalf20@ideweb2.hh.se:/public_html/

rm -rf ../tmp ../deploy