<?php 
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */


define('br', "<br />\n\r");
define('nl', "\r\n");
define('content', '<!-- content:'. time() .' -->');
define('ip', server('REMOTE_ADDR'));
define('is_post', (server('REQUEST_METHOD') == 'POST'));
define('is_ajax', server('HTTP_X_REQUESTED_WITH') && strtolower(server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
define('site_url', 'http'. (server('HTTPS') ? 's' : '') .'://'. server('SERVER_NAME') . ROOT_VIRTUAL);

define('minute', 60);
define('hour', 3600);
define('day', 86400);
define('month', 2592000);
define('year', 31536000);


define('BR', "<br />\n\r");
define('NL', "\r\n");
define('CONTENT', '<!-- content:'. time() .' -->');
define('IP', server('REMOTE_ADDR'));
define('IS_POST', (server('REQUEST_METHOD') == 'POST'));
define('IS_AJAX', (server('HTTP_X_REQUESTED_WITH')) && strtolower(server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
define('SITE_URL', 'http'. ((server('HTTPS')) ? 's' : '') .'://'. server('SERVER_NAME') . rtrim(ROOT_VIRTUAL, '/') . '/');
define('URL', SITE_URL . ((server('PATH_INFO')) ? trim(server('PATH_INFO'), '/') : '') . ((server('QUERY_STRING')) ? server('QUERY_STRING') : ''));

define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('MONTH', 2592000);
define('YEAR', 31536000);