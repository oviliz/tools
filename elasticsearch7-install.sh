#!/bin/bash

# Update system
apt -y update
apt -y upgrade
apt install -y apt-transport-https

# Import Elasticsearch PGP Key
apt -y install gnupg
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -

# Add APT repository
sh -c 'echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" > /etc/apt/sources.list.d/elastic-7.x.list'

# Install Elasticsearch
apt -y update
apt -y install elasticsearch

# Set ES memory
sed -i 's/Xms2g/Xms512m/g' /etc/elasticsearch/jvm.options
sed -i 's/Xmx2g/Xmx512m/g' /etc/elasticsearch/jvm.options

# ES remote access
sed -i 's/#network.host: 192.168.0.1/network.host: 0.0.0.0/g' /etc/elasticsearch/elasticsearch.yml

# ES single node
sed -i 's/#discovery.seed_hosts: \[\"host1\", \"host2\"\]/discovery.seed_hosts: \[\"127.0.0.1\"\]/g' /etc/elasticsearch/elasticsearch.yml

systemctl daemon-reload
systemctl enable elasticsearch.service
systemctl start elasticsearch.service
systemctl status elasticsearch.service
