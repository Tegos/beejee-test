<?php

session_start();

include "functions.php";

$app = new \app\system\App();

$app->run($_SERVER['REQUEST_URI']);