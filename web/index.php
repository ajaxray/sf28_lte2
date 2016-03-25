<?php

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod'));

define('WEB_PATH', __DIR__);

if (APPLICATION_ENV == 'prod') {
    require 'app.php';
} else {
    require 'app_dev.php';
}
