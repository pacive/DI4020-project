#!/bin/bash

PROJECT_URI='https://ideweb2.hh.se/~andalf20/project_test/'
VALIDATOR_URI='https://validator.w3.org/nu/?out=gnu'

html_files=$(find . -type f -name "*.html")
css_files=$(find . -type f -name "*.css")
php_files=*.php

error=0

for file in $html_files
do
  echo $file
  result=$(curl -s -H "Content-type: text/html; charset=utf-8" --data-binary @$file $VALIDATOR_URI)
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
  result=$(curl -s -H "Content-type: text/css; charset=utf-8" --data-binary @$file $VALIDATOR_URI)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

curl -s --cookie-jar ../tmp/cookies.txt -X POST -d "$PAGE_AUTH" ${PROJECT_URI}api/auth.php
for file in $php_files
do
  echo $file
  if [ "$file" == "template.php" ]; then
    continue
  fi
  curl -s --cookie ../tmp/cookies.txt ${PROJECT_URI}$file > page.html
  result=$(curl -s -H "Content-type: text/html; charset=utf-8" --data-binary @page.html $VALIDATOR_URI)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

exit $error
