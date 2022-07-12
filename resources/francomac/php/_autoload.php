<?php
require_once(__DIR__.'\utils\sysutils.php');

$listPhpFiles = listFiles(__DIR__,'*.php');
foreach ($listPhpFiles as $value)
{
   require_once($value);
}
?>
