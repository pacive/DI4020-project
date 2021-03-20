#!/bin/bash

html_files=$(find . -type f -name "*.html")
css_files=$(find . -type f -name "*.css")

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
  result=$(curl -s -H "Content-type: text/html; charset=utf-8" --data-binary @$file https://validator.w3.org/nu/?out=text)
  if [ "$result" == "" ]; then
    echo valid!
  else
    echo "$result"
    error=1
  fi
done

exit $error