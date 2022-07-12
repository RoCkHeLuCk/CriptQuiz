<?php
namespace Test;
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
      $this->request->add('senha', TRequest::IN_POST,
         function ($value)
         {
            return superTrim($value);
         }
      );
   }

   public function execute()
   {
      if (!isset($_SESSION['teste']))
      {
         $_SESSION['teste'] = 3;
      }

      if (ifset($_SESSION,'system') == 'lock')
      {
         header('Location: ./?PG=Lock');
         exit;
      }

      $this->view->loadFile(__DIR__.'/view/test.html');

      if ($_POST)
      {
         $r = $this->palavraTeste()
            [$this->respostaTeste($_SESSION['data'][1])];
         if($this->request->senha == $r)
         {
            $_SESSION['system'] = 'unlock';
            header('Location: ./?PG=System');
         }else{
            $this->view->alert->attribute('class')->del('d-none');
            $this->view->alert->replaceStr('tentativa',$_SESSION['teste']);
            $_SESSION['teste']--;
            if ($_SESSION['teste'] < 0)
            {
               $_SESSION['system'] = 'lock';
               header('Location: ./?PG=Lock');
            }
         }
      }

      $br = new TView();
      $br->loadText('<br/>');
      $c = 1;
      foreach ($this->palavraTeste() as $value)
      {
         $this->view->chave->insertOf( sprintf('%02d : ',$c).
               $this->encript5($value,$_SESSION['pc']));
         $this->view->chave->insertOf($br);
         $c++;
      }

      return $this->view;
   }

   private function encript5(string $value, int $pc) : string
   {
      $value = strtolower(strrev($value));
      $replace = array(
         'a' => sprintf('%02d',1+$pc),
         'e' => sprintf('%02d',2+$pc),
         'i' => sprintf('%02d',3+$pc),
         'o' => sprintf('%02d',4+$pc),
         'u' => sprintf('%02d',5+$pc));
      $value = str_replace(array_keys($replace),$replace,$value);
      $value = str_replace(' ','$',$value);
      return $value.'/'.strlen($value);
   }

   private function palavraTeste() : array
   {
      $palavras = array();
      $palavras[1] = 'seu futuro no senai';//Servico Nacional
      $palavras[2] = 'de Aprendizagem Industrial';
      $palavras[3] = 'e uma instituicao privada';
      $palavras[4] = 'brasileira de interesse publico';
      $palavras[5] = 'sem fins lucrativos';
      $palavras[6] = 'informatica para intenet'; //12
      $palavras[7] = 'programacao para iot'; //1
      $palavras[8] = 'tecnico em automacao'; //3
      $palavras[9] = 'protecao de ameaca digital';//10
      $palavras[10] = 'aprenda criptografia'; //6
      $palavras[11] = 'construindo sites'; //4
      $palavras[12] = 'programando sua vida'; //5
      $palavras[13] = 'construa seu futuro'; //9
      $palavras[14] = 'se prepare para nova geracao'; //7
      $palavras[15] = 'obtenha conhecimento'; //8
      $palavras[16] = 'fique sempre em primeiro lugar'; //11
      $palavras[17] = 'tecnico em eletrotecnica'; //2
      $palavras[18] = 'com personalidade juridica';
      $palavras[19] = 'de direito privado';
      $palavras[20] = 'estando fora da';
      $palavras[21] = 'administracao publica';

      return $palavras;
   }

   private function respostaTeste(int $mes) : string
   {
      $palavras = array();
      $palavras[] = 7;
      $palavras[] = 17;
      $palavras[] = 8;
      $palavras[] = 11;
      $palavras[] = 12;
      $palavras[] = 10;
      $palavras[] = 14;
      $palavras[] = 15;
      $palavras[] = 13;
      $palavras[] = 9;
      $palavras[] = 16;
      $palavras[] = 6;
      return $palavras[$mes-1];
   }

}

?>
