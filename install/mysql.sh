#!/bin/sh -e
#
# $Id: mysql.sh,v 1.11 2006/01/11 22:51:28 anarcat Exp $
# ----------------------------------------------------------------------
# AlternC - Web Hosting System
# Copyright (C) 2002 by the AlternC Development Team.
# http://alternc.org/
# ----------------------------------------------------------------------
# Based on:
# Valentin Lacambre's web hosting softwares: http://altern.org/
# ----------------------------------------------------------------------
# LICENSE
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License (GPL)
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# To read the license please visit http://www.gnu.org/copyleft/gpl.html
# ----------------------------------------------------------------------
# Original Author of file: Benjamin Sonntag
# Purpose of file: Install a fresh new mysql database system
# USAGE : "mysql.sh loginroot passroot systemdb"
# ----------------------------------------------------------------------
#
rootlogin=$1
rootpass=$2
systemdb=$3

datadir=/var/alternc/db

mysql="mysql --defaults-file=/etc/mysql/debian.cnf"

# move the rundir to the postfix chroot and symlink the original
sockdir=/var/run/mysqld
postsockdir=/var/spool/postfix/$sockdir
mkdir -p /var/spool/postfix/var/run
if [ ! -h $sockdir ]
then
    mv $sockdir $postsockdir
    ln -s $postsockdir $sockdir
fi

mkdir -p $datadir
echo -n "making sure we have our basic database structure in place"
mysql_install_db --datadir=$datadir --user=mysql 2>&1 > /dev/null
echo .

# Write a temporary /etc/mysql/debian.cnf
cp -a -f /etc/mysql/debian.cnf /etc/mysql/debian.cnf.tmp
cat << EOF > /etc/mysql/debian.cnf
[client]
host     = localhost
user     = root
socket   = /var/run/mysqld/mysqld.sock
EOF

/etc/init.d/mysql start 2>&1 > /dev/null

if ! $mysql mysql -e "SHOW TABLES" >/dev/null
then
    # is this an upgrade then?
    mysql="mysql -u $rootlogin -p$rootpass" 
    if ! $mysql mysql -e "SHOW TABLES" >/dev/null
    then
        echo "Can't get proper credentials, aborting"
        exit 1
    fi
fi

echo "Setting AlternC $systemdb system table and privileges "
$mysql -e "CREATE DATABASE IF NOT EXISTS $systemdb;" 
echo "Installing AlternC schema "
$mysql $systemdb < /usr/share/alternc/install/mysql.sql

echo "Granting users "
$mysql -e "GRANT ALL ON *.* TO '$rootlogin'@'localhost' IDENTIFIED BY '$rootpass' WITH GRANT OPTION" 

myrandom=`sed -n -e '/^password/s/password = //p' < /etc/mysql/debian.cnf.tmp`

$mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'debian-sys-maint'@'localhost' IDENTIFIED BY '$myrandom' WITH GRANT OPTION; "
$mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'debian-sys-maint'@'localhost.localdomain' IDENTIFIED BY '$myrandom' WITH GRANT OPTION; "
# drop the root user
$mysql -e "REVOKE ALL ON *.* FROM 'root'@'localhost'" 
echo .

mysql -u $rootlogin -p$rootpass $systemdb -e "SHOW TABLES" >/dev/null && echo "MYSQL.SH OK!" || echo "MYSQL.SH FAILED!"

# Move back original to debian.cnf
mv /etc/mysql/debian.cnf.tmp /etc/mysql/debian.cnf
/etc/init.d/mysql stop
