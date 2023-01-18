sudo nano /etc/yum.repos.d/mongodb-org-5.0.repo

    [mongodb-org-6.0]
    name=MongoDB Repository
    baseurl=https://repo.mongodb.org/yum/redhat/$releasever/mongodb-org/6.0/x86_64/
    gpgcheck=1
    enabled=1
    gpgkey=https://www.mongodb.org/static/pgp/server-6.0.asc



sudo yum install -y mongodb-org

sudo yum -y update

sudo yum -y install gcc php-pear php-devel
sudo yum install php-ldap

sudo pecl install mongodb-1.12.0


sudo nano /etc/opt/remi/php74/php.ini
    add extension=/usr/lib64/php/modules/mongodb.so   
    add extension=/usr/lib64/php/modules/zip.so   

sudo systemctl start mongod
sudo systemctl restart php-fpm.service

> Install composer

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

composer require --ignore-platform-reqs mongodb/mongodb:1.12.0
composer require --ignore-platform-reqs phpoffice/phpword
mongorestore  dump/

composer update --ignore-platform-reqs