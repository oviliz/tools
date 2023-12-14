#!/bin/bash

### BitNinja.io blacklist from NGINX access logs

if cat /var/log/nginx/website_nginx.access.log | grep aspx | $ awk '{print $1}';
then bitninjacli --blacklist --check=$awk;
fi

for OUTPUT in $(cat /var/log/nginx/website/website_nginx.access.log | grep aspx | awk '{print $1}')
do
 bitninjacli --blacklist --check= on $OUTPUT
done

$ IPAddresses=($(awk '{print $2}' abd))
$ echo "${IPAddresses[@]}"
