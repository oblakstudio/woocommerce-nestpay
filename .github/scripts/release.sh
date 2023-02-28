#!/bin/bash

PROJECT_NAME="wc-serbian-nestpay"
NEXT_VERSION=$1
CURRENT_VERSION=$(cat $PROJECT_NAME.php | grep "Version" | head -1 | awk -F= "{ print $2 }" | sed 's/[* Version:,\",]//g' | tr -d ':space:')

sed -i "s/Version.*/Version:              $NEXT_VERSION/g" wc-serbian-nestpay.php
sed -i "s/^Stable tag:.*/Stable tag: $NEXT_VERSION/g" ./.wordpress-org/readme.txt
sed -i "s/'$CURRENT_VERSION'/'$NEXT_VERSION'/g" ./lib/WooCommerce_Nestpay.php

rm -f /tmp/release.zip
rm -rf /tmp/$PROJECT_NAME*
mkdir /tmp/$PROJECT_NAME
cp -ar config dist languages lib vendor ./*.php loco.xml /tmp/$PROJECT_NAME 2>/dev/null
cp ./.wordpress-org/readme.txt /tmp/$PROJECT_NAME 2>/dev/null

cd /tmp || exit
zip -qr /tmp/release.zip ./*.php $PROJECT_NAME
