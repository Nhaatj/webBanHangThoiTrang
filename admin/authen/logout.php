<?php
session_start();

require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');

$token = getCookie('token');
setcookie('token','',time() - 100, '/');

session_destroy();

header('Location: login.php');