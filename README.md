# Voicelogger-App-With-Ast-1.8++++

#install php-intl required for CI 4.3

yum install php-intl

Create recording link in html folder 



recordings —-> /var/www/recordings



or 



For Recording Player create file in /etc/httpd/conf.d/recordings.conf



#paste below mention lines

Alias /recordings "/var/www/recordings/"

Alias /RECORDINGS "/var/www/recordings/"

<Directory "/var/www/recordings/files/">

Options Indexes FollowSymLinks

	AllowOverride All

	Require all granted

</Directory>

************************************

Enable access_log 



add following line into the /etc/httpd/conf/httpd.conf access log section

# server as '/www/log/access_log', where as '/log/access_log' will be
# interpreted as '/log/access_log'.

CustomLog logs/access_log combined

***************************************************************

For voicelogger application access with php spark



Open /etc/httpd/conf/httpd.conf



#at bottom of file add below mention lines



Alias /voicelogger2 "/var/www/html/voicelogger2/public"

<Directory "/var/www/html/voicelogger2/public/">

Options Indexes FollowSymLinks

	AllowOverride All

	Require all granted

</Directory>

***********************************************************

Save and restart httpd service 







my.cnf file



cat /etc/my.cnf

[mysqld]

datadir = /var/lib/mysql

server_id=1

log-basename=master

#log-bin

binlog-format=row

binlog-do-db=voicecatch

log_bin = /var/log/mysql/mysql-bin.log

bind-address = 0.0.0.0

socket = /var/lib/mysql/mysql.sock

log-error = /var/log/mysqld/mysqld.log

log-bin = mysql-bin

log-warnings=3

sql_mode="NO_ENGINE_SUBSTITUTION"

# make sure sql_mode- line is there in my.cnf else callentry not insert into DB due to datetime #issue.





#

# include all files from the config directory

#

!includedir /etc/my.cnf.d



#### ADD CRON ACCESS TO VOICECATCH

GRANT SELECT,CREATE,ALTER,INSERT,UPDATE,DELETE,LOCK TABLES on voicecatch.* TO cron@'%' IDENTIFIED BY '1234';
GRANT SELECT,CREATE,ALTER,INSERT,UPDATE,DELETE,LOCK TABLES on voicecatch.* TO cron@localhost IDENTIFIED BY '1234';
GRANT RELOAD ON *.* TO cron@'%';
GRANT RELOAD ON *.* TO cron@localhost;
flush privileges;

SET GLOBAL connect_timeout=60;
