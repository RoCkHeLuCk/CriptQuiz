<?php
namespace System;
use FrancoMAC\MVC\TController;
use FrancoMAC\MVC\TView;
use FrancoMAC\Extras\TRequest;

class Controller extends TController
{
   private $view = NULL;
   private $request = NULL;

   public function __construct()
   {
      $this->view = new TView();
      $this->request = new TRequest();
   }

   public function execute()
   {
      if (!isset($_SESSION['system']))
      {
         header('Location: ./?PG=404');
         exit;
      }else{
         if ($_SESSION['system'] == 'lock')
         {
            header('Location: ./?PG=Lock');
            exit;
         }
      }

      if (!isset($_SESSION['luz']))
      {
         $_SESSION['luz'] = false;
      }

      $this->view->loadFile(__DIR__.'/view/sistema.html');

      if ($_POST)
      {
         if(isset($_POST['luz']))
         {
            $value = $this->arduino($_SESSION['luz']);
            if ($value !== NULL)
            {
               $_SESSION['luz'] = $value;
            } else {
               $this->view->alert2->attribute('class')->del('d-none');
            }
         }
      }

      $this->view->buttao->replaceStr('onoff',$_SESSION['luz']?'Ligar':'Desligar');
      return $this->view;
   }

   private function arduino(bool $ligar) : ?float
   {
      $port = "COM3";
      $prompt = exec("MODE $port BAUD=9600 PARITY=n DATA=8 XON=on STOP=1");
      if ($prompt == '')
      {
         $fp = fopen($port, 'w');
         if ($fp)
         {
            fwrite($fp, $ligar ? 'l' : 'd');
            fclose($fp);
         }
         return !$ligar;
      }
      return NULL;
   }

}

?>
