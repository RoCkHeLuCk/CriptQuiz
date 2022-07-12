<?php
require_once('sysutils.php');

/**
 *   Record the microtime of the beginning of the test
 *
 *   @var   float
 */
$GLOBALS['timeTest'] = 0;

/**
 *   start Time Test
 *
 *   @method   startTimeTest
 */
function startTimeTest() : void
{
   $GLOBALS['timeTest'] = microtime(true);
}

/**
 *   stop Time Test return interval
 *
 *   @method   stopTimeTest
 *   @return   string   numeric formated prefix
 */
function stopTimeTest() : string
{
   $time_end = microtime(true) - $GLOBALS['timeTest'];
   $time_end *= 1000000;
   return numberPrefix($time_end, false, array('u','m','')).'s';
}

/**
 *   return report server Performance Test
 *
 *   @method   serverPerforTest
 *   @return   array           [time], [memory], [memory_true]
 */
function serverPerforTest() : array
{
   $time_end = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
   $time_end *= 1000000;
   $result = array();
   $result['time'] = numberPrefix($time_end,false,array('u','m','')).'s';
   $result['memory'] =  numberPrefix(memory_get_usage(false), true).'B';
   $result['memory_true'] =  numberPrefix(memory_get_usage(true), true).'B';
   return $result;
}

/**
 *   send message to browser console
 *
 *   @method   consoleLog
 *   @param    mixed       $message
 */
function consoleLog($message) : void
{
   $message = serialize($message);
   echo "<script>
            console.log( '$message' );
         </script>";
}

?>
