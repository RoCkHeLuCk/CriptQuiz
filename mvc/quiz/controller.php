<?php
namespace Quiz;
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
      $this->request->add('Q',TRequest::IN_GET,
         function ($value)
         {
            $value = ifnumeric($value,0);
            return ((0 <= $value)&&($value <= 6))?$value:NULL;
         }
      );
      $this->request->add('resposta',TRequest::IN_POST,
         function ($value)
         {
            return superTrim($value);
         }
      );
   }

   public function execute()
   {
      if (!isset($_SESSION['quiz']))
      {
         $_SESSION['quiz'] = 0;
      }

      $Q = $this->request->Q;
      if (is_null($Q))
      {
         $this->setQuiz(0);
      }else{
         if ($Q > $_SESSION['quiz'])
         {
            $this->setQuiz($_SESSION['quiz']);
         }
      }

      $this->view->loadFile(__DIR__."/view/q$Q.html");
      //$this->view->pergunta->replaceStr('teste',$this->encriptacao());
      $nome = '';
      $teste = '';
      $this->request->resposta = strtolower($this->request->resposta);
      switch ($Q)
      {
         case 0:
         {
            $nome = $_SESSION['nome'];
         } break;

         case 1:
         {
            $nome = strrev($_SESSION['nome']);
         } break;

         case 2:
         {
            $nome = $this->encript2($_SESSION['nome']);
         } break;

         case 3:
         {
            $nome = $this->encript3($_SESSION['nome'],$_SESSION['pc']);
         } break;

         case 4:
         {
            $nome = $this->encript4($_SESSION['nome'],$_SESSION['pc']);
         } break;

         case 5:
         {
            $nome = $this->encript5($_SESSION['nome'],$_SESSION['pc']);
            $teste = $this->palavraTeste($_SESSION['data'][1]);
            $teste = $this->encript5($teste,$_SESSION['pc']);
         } break;

         case 6:
         {
            $nome = $this->palavraTeste($_SESSION['data'][1]);
            if (isset($_SESSION['system']))
               unset($_SESSION['system']);
         } break;
      }

      if ($_POST)
      {
         switch ($Q)
         {
            case 0:
            {
               if ($this->request->resposta == strrev($_SESSION['nome']))
               {
                  $this->setQuiz(1);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;

            case 1:
            {
               if ($this->request->resposta ==
                  $this->encript2($_SESSION['nome']))
               {
                  $this->setQuiz(2);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;

            case 2:
            {
               if ($this->request->resposta ==
                  $this->encript3($_SESSION['nome'],$_SESSION['pc']))
               {
                  $this->setQuiz(3);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;

            case 3:
            {
               if ($this->request->resposta ==
                  $this->encript4($_SESSION['nome'],$_SESSION['pc']))
               {
                  $this->setQuiz(4);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;

            case 4:
            {
               if ($this->request->resposta ==
                  $this->encript5($_SESSION['nome'],$_SESSION['pc']))
               {
                  $this->setQuiz(5);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;

            case 5:
            {
               $ttt = $this->palavraTeste($_SESSION['data'][1]);
               if ($this->request->resposta == $ttt)
               {
                  $this->setQuiz(6);
               }else{
                  $this->view->alert->attribute('class')->del('d-none');
               }
            } break;
         }
      }

      $this->view->pergunta->replaceStr('nome',$nome);
      $this->view->pergunta->replaceStr('pc',$_SESSION['pc']);
      $this->view->pergunta->replaceStr('teste',$teste);

      if ($_SESSION['quiz'] == $Q)
        $this->view->next->deleteMe();
      return $this->view;
   }

   private function encript2(string $value) : string
   {
      $value = strtolower(strrev($value));
      $replace = array(
         'a' => '01',
         'e' => '02',
         'i' => '03',
         'o' => '04',
         'u' => '05');
      return str_replace(array_keys($replace),$replace,$value);
   }

   private function encript3(string $value, int $pc) : string
   {
      $value = strtolower(strrev($value));
      $replace = array(
         'a' => sprintf('%02d',1+$pc),
         'e' => sprintf('%02d',2+$pc),
         'i' => sprintf('%02d',3+$pc),
         'o' => sprintf('%02d',4+$pc),
         'u' => sprintf('%02d',5+$pc));
      return str_replace(array_keys($replace),$replace,$value);
   }

   private function encript4(string $value, int $pc) : string
   {
      $value = $this->encript3($value,$pc);
      return str_replace(' ','$',$value);
   }

   private function encript5(string $value, int $pc) : string
   {
      $value = $this->encript4($value,$pc);
      return $value.'/'.strlen($value);
   }

   private function palavraTeste(int $mes) : string
   {
      $palavras = array();
      $palavras[] = 'quatorze menos sete';// = 7
      $palavras[] = 'dezoito menos um';// = 17
      $palavras[] = 'quatro mais quatro';// = 8
      $palavras[] = 'sete mais quatro';// = 11
      $palavras[] = 'vinte menos oito';// = 12
      $palavras[] = 'quinze menos cinco';// = 10
      $palavras[] = 'doze mais dois';// = 14
      $palavras[] = 'nove mais seis';// = 15
      $palavras[] = 'dezoito menos cinco';// = 13
      $palavras[] = 'quatorze menos cinco';// = 9
      $palavras[] = 'treze mais tres';// = 16
      $palavras[] = 'oito menos dois';// = 6

      return $palavras[$mes-1];
   }

   private function setQuiz(int $value) : void
   {
      if ($value > $_SESSION['quiz'])
      {
         $_SESSION['quiz'] = $value;
         $users = parse_ini_file(__DIR__ . '/../../users.ini', true);
         $users[$_SESSION['pc']]['progress'] = 'Quiz '.$_SESSION['quiz'];
         write_to_ini(__DIR__ . '/../../users.ini', $users);
      }
      header('Location: ./?PG=Quiz&Q='.$_SESSION['quiz']);
      exit;
   }
}

?>
