<?php
require_once '../../utils/autoload.php';

session_start();
session_destroy();
setcookie('auth');
header("Location: ../inicial/");