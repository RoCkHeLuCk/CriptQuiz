<?php
namespace FrancoMAC\Extras;
require_once(__DIR__.'/../mvc/base.php');
use FrancoMAC\MVC\TBase;
use FrancoMAC\MVC\TView;
use Exception;

/**
 *   TViewList
 *
 *   List of Elements, DOMDocument
 */
class TElementList extends TBase
{
   /**
    *   List TView
    *   @var  TView
    */
   private $elementList = array();

   /**
    *   Construct List
    *
    *   @method   __construct
    *   @param    string        $path
    */
   public function __construct(string $path)
   {
      if (!is_dir($path))
      {
         throw new Exception(
            "TElementList ERROR: Directory $path not found"
         );

      }
      $listFiles = listFiles($path,'*.html');
      foreach ($listFiles as $filePath)
      {
         $fileName = basename($filePath,'.html');
         $this->elementList[$fileName] = new TView();
         $this->elementList[$fileName]->loadFile($filePath);
      }
   }

   /**
    *   Get item to list
    *
    *   @method   __get
    *   @param    string   $id
    *   @return   TView
    */
   public function __get(string $id) : TView
   {
      if (array_key_exists($id,$this->elementList))
      {
         return $this->elementList[$id];
      }else{
         throw new Exception(
            "TElementList ERROR: $id not found"
         );
      }
   }
}
?>
