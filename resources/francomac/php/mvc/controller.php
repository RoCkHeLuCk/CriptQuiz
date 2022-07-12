<?php
namespace FrancoMAC\MVC;
require_once(__DIR__.'/base.php');
use FrancoMAC\MVC\TBase;

/**
 *   TController
 *
 *   Class Controller
 */
class TController extends TBase
{
   /**
    *   Define PRIVILEGES Bitwise
    *   @var   int
    */
   private static $PRIVILEGES = 0;

   /**
    *   add privilege Controler access Bitwise
    *
    *   @method   addPrivilege
    *   @param    int            $privilege
    */
   protected function addPrivilege(int $privilege) : void
   {
      TController::$PRIVILEGES =
         TController::$PRIVILEGES | $privileges;
   }

   /**
    *   delete privilege controler access Bitwise
    *
    *   @method   delPrivilege
    *   @param    int            $privilege
    */
   protected function delPrivilege(int $privilege) : void
   {
      TController::$PRIVILEGES =
         TController::$PRIVILEGES & ~$privileges;
   }

   /**
    *   test if you have the pribilegio Bitwise
    *
    *   @method   hasPrivilege
    *   @param    int           $privilege
    *   @return   bool
    */
   protected function hasPrivilege(int $privilege) : bool
   {
      return (TController::$PRIVILEGE & $privilege) != 0;
   }

   /**
    *   load Controller and namespace, execute method "action"
    *   @method   loadController
    *   @param    string           $fileName
    *   @param    string           $action
    *   @return   mixed
    */
   protected function loadController(string $fileName,
      string $action = NULL)
   {
      if (file_exists($fileName) and require_once($fileName))
      {
         $namespace = str_replace( "/", DIRECTORY_SEPARATOR,
               dirname($fileName) );

         $class = $namespace. '/'. basename($fileName,'.php');
         $controller = new $class();
         if ($controller)
         {
            if ($action
            and (substr($action,0,1) != '_')
            and method_exists($controller, $action))
            {
               return $controller->$action();
            }else{
               return $controller->execute();
            }
         }
      }else{
         if ($this->isDebug())
   	   {
   	      echo "TController ERROR: File ($fileName) dont found";
   	   }
      }
      return NULL;
   }
}
?>
