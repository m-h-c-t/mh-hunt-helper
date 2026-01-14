<?php

if (!defined('not_direct_access')) {
    error_log(__FILE__ . " direct access error");
	header("HTTP/1.0 404 Not Found");
    die();
}
