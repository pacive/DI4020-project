#!/bin/bash

mkdir -p ../tmp
mkdir -p ../deploy
cp -r * ../deploy

directories=$(find ../deploy -type d | cut -sd / -f3-)

echo "lcd ../deploy" >> ../tmp/upload.txt
echo "-mkdir project_test" >> ../tmp/upload.txt

for dir in $directories; do
        echo "-mkdir project_test/${dir}" >> ../tmp/upload.txt
done

echo "-put -r . project_test/" >> ../tmp/upload.txt

sshpass -e sftp -P 20022 -oBatchMode=no -b ../tmp/upload.txt andalf20@ideweb2.hh.se:/public_html/