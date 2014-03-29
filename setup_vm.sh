#!/bin/bash
# Stop completely on any error
set -e

# Check to see if this script has already been run
# Only needs to be done the first time the machine is brought up
if [[ -e /home/vagrant/.installed ]]; then exit; fi

# Install Packages
PACKAGES="apache2 mysql-server php5 php5-mysql"
apt-get update
# Allows MySQL to be installed w/o glyph problems from root password prompt
DEBIAN_FRONTEND=noninteractive aptitude install -q -y $PACKAGES

# Setup Represent Map
cat /vagrant/represent-map/include/db_example.php | \
sed s/"\[db_host\]"/localhost/ | \
sed s/"\[db_name\]"/representmap/ | \
sed s/"\[db_user\]"/root/ | \
sed s/"\[db_pass\]"/password/ | \
sed s/"\[admin_user\]"/admin/ | \
sed s/"\[admin_pass\]"/admin/ > /vagrant/represent-map/include/db.php

# Setup MySQL
mysql -uroot -e \
"UPDATE mysql.user SET password=PASSWORD('password') WHERE user='root'; \
CREATE DATABASE representmap; \
FLUSH PRIVILEGES;"

mysql -uroot -ppassword representmap < /vagrant/represent-map/db/events_1.sql
mysql -uroot -ppassword representmap < /vagrant/represent-map/db/places_3.sql
mysql -uroot -ppassword representmap < /vagrant/represent-map/db/settings_1.sql

# Setup Apache
cat > /etc/apache2/sites-enabled/000-default <<EOS
<VirtualHost *:80>
    DocumentRoot /vagrant/represent-map

  <Directory /vagrant/represent-map/>
    Options FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
  </Directory>

  <Directory ~ "\.git">
    Order allow,deny
    Deny from all
  </Directory>
</VirtualHost>
EOS

/etc/init.d/apache2 restart

# Set lock file so this script won't run next time
touch /home/vagrant/.installed

cat <<EOS
Represent Map should now be running on http://localhost:8080
---
The credentials are as follows:
db_host: localhost
db_name: representmap
db_user: root
db_pass: password
admin_user: admin
admin_pass: admin

This is intended to be for testing/development purposes.
You should ABSOLUTELY NOT use this script for a production install without
changing the config in represent-map/include/db.php first!
EOS
