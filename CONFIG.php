<?php
// define root path dependent on the server name
if (in_array($_SERVER['SERVER_NAME'], ['testserver', 'localhost', 'juk20-dev.dsmz.local'])) {
    // subfolder in my test servers
    define('ROOTPATH', '/osiris');
} else {
    define('ROOTPATH', '');
}

define('ADMIN', 'juk20');