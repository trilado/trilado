<?php 
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */


define('br', "<br />\n\r");
define('nl', "\r\n");
define('content', '<!-- content:'. time() .' -->');
define('ip', $_SERVER['REMOTE_ADDR']);
define('is_post', ($_SERVER['REQUEST_METHOD'] == 'POST'));
define('is_ajax', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('site_url', 'http'. (isset($_SERVER['HTTPS']) ? 's' : '') .'://'. $_SERVER['SERVER_NAME'] . ROOT_VIRTUAL);

define('minute', 60);
define('hour', 3600);
define('day', 86400);
define('month', 2592000);
define('year', 31536000);


define('BR', "<br />\n\r");
define('NL', "\r\n");
define('CONTENT', '<!-- content:'. time() .' -->');
define('IP', $_SERVER['REMOTE_ADDR']);
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST'));
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('SITE_URL', 'http'. (isset($_SERVER['HTTPS']) ? 's' : '') .'://'. $_SERVER['SERVER_NAME'] . rtrim(ROOT_VIRTUAL, '/') . '/');
define('URL', SITE_URL . (isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '') . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''));

define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('MONTH', 2592000);
define('YEAR', 31536000);