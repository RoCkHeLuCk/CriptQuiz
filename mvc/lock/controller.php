<?php
namespace Lock;
use FrancoMAC\MVC\TController;
use FrancoMAC\MVC\TView;
use FrancoMAC\Extras\TRequest;

class Controller extends TController
{
   private $view = NULL;

   public function __construct()
   {
      $this->view = new TView();
   }

   public function execute()
   {
      $this->view->loadFile(__DIR__.'/view/lock.html');
      $PC = explode('.',$_SERVER['REMOTE_ADDR']);
      $PC = count($PC)==4?$PC[3]:'0';

      if ($PC <= 20)
      {
         $PC = 'Computador: '.$PC;
      }else{
         $PC = 'SmartPhone: '.$PC;
      }
      $this->view->pergunta->replaceStr('pc',$PC);
      return $this->view;
   }
}

?>
