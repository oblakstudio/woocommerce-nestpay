#/bin/bash

PLUGIN_NAME="woocommerce-nestpay-payment-gateway"
PLUGIN_FILE="woocommerce-nestpay.php"

NEXT_VERSION=$1
CURRENT_VERSION=$(cat $PLUGIN_FILE | grep Version | head -1 | awk -F= "{ print $2 }" | sed 's/[Version:,\",]//g' | tr -d '[[:space:]]')

sed -i "s/Version:              $CURRENT_VERSION/Version:              $NEXT_VERSION/g" $PLUGIN_FILE

mkdir /tmp/$PLUGIN_NAME
cp -ar ./*.php woocommerce vendor framework dist config lib languages /tmp/$PLUGIN_NAME 2>/dev/null
cd /tmp
zip -qr /tmp/$PLUGIN_NAME-$NEXT_VERSION.zip $PLUGIN_NAME
