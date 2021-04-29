#!/bin/bash

html_files=$(find . -type f -name "*.html")
css_files=$(find . -type f -name "*.css")
php_files=$(ls *.php)

error=0

for file in $html_files
do
  echo $file
  result=$(curl -s -H "Content-type: text/html; charset=utf-8" --data-binary @$file https://validator.w3.org/nu/?out=gnu)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

for file in $css_files
do
  echo $file
  result=$(curl -s -H "Content-type: text/css; charset=utf-8" --data-binary @$file https://validator.w3.org/nu/?out=gnu)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

curl -H "Authorization: $PROXY_AUTH" --cookie-jar cookies.txt -X POST -d "$PAGE_AUTH" https://project-proxy.alfredsson.info/api/auth.php
for file in $php_files
do
  echo $file
  if [ "$file" == "template.php" ]; then
    continue
  fi
  curl -s -H "Authorization: $PROXY_AUTH" --cookie cookies.txt https://project-proxy.alfredsson.info/$file > page.html
  result=$(curl -s -H "Content-type: text/html; charset=utf-8" --data-binary @page.html https://validator.w3.org/nu/?out=gnu)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

exit $error
