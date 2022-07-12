<?php
session_start();
require_once(__DIR__.'\resources\francomac\php\_autoload.php');
require_once(__DIR__.'\mvc\index\controller.php');
use Index\Controller;
$ctrl = new Controller();
$ctrl->execute();
?>
