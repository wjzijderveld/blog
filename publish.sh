#!/bin/bash

SCULPIN=$(which sculpin)

if [ -z $SCULPIN ]; then
    echo "Sculpin not found"
    exit 1
fi

$SCULPIN generate --env=-prod

rsync -vzr ./output/* wjzijderveld@vps1891.directvps.nl:/var/customers/webs/wjzijderveld/blog.willem-jan.net/httpdocs/
