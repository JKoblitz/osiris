
# Set up OSIRIS

1. Make sure you have the following PHP modules available:
   - PHP (tested with versions 7.4, 8.0, 8.1)
   - MongoDB Driver (`pecl install mongodb-1.12.0`)
   - LDAP (`yum install php-ldap`)
2. Update `settings.json`
3. Install MongoDB (Version 5)
4. Initialize a new database `osiris` with the collection `users`
   - In case you choose another database name, you can update it in the [_db.php](php/_db.php) file.
5. Install composer 
6. Run `composer update --ignore-platform-reqs`
   - It will install the MongoDB module and PHPWord
7. Configure LDAP 
8. Go to the installed web page and log-in with your user account

All other collections will be created and filled once you use the web page.


