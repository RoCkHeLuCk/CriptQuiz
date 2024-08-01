<?php
namespace Login;
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
      $this->view->loadFile(__DIR__.'/view/login.html');
      $this->request = new TRequest();
      $this->request->add('nome', TRequest::IN_POST,
         function ($value)
         {
            return strtolower(removeAccents(superTrim($value)));
         }
      );
      $this->request->add('data', TRequest::IN_POST);
      $this->request->add('senha', TRequest::IN_POST);
   }

   public function execute()
   {
      $PC = explode('.',$_SERVER['REMOTE_ADDR']);
      $PC = count($PC)==4?$PC[3]:'0';
      $users = parse_ini_file(__DIR__ . '/../../users.ini', true);
      if (!issets($users, $PC))
         $users[$PC] = array();

      if ($_POST)
      {
         $data = explode('-',$this->request->data);
         $senha = $data[0]+$data[1]+$data[2]+$PC;

         if ($this->request->senha == $senha)
         {
            if (ifset($users[$PC],'name', $this->request->nome)
               == $this->request->nome)
            {
               $_SESSION = array();
               $_SESSION['nome'] = $this->request->nome;
               $_SESSION['data'] = $data;
               $_SESSION['senha'] = $this->request->senha;
               $_SESSION['pc'] = $PC;


               if (ifset($users[$PC], 'name') == '') {
                  $users[$PC]['name'] = $_SESSION['nome'];
               }

               if (ifset($users[$PC], 'progress') == '') {
                  $users[$PC]['progress'] = 'Inciou!';
               }

               if (ifset($users[$PC], 'start') == '') {
                  $_SESSION['tempo'] = date("H:i:s");
                  $users[$PC]['start'] = $_SESSION['tempo'];
               } else {
                  $_SESSION['tempo'] = $users[$PC]['start'];
               }
               write_to_ini(__DIR__ . '/../../users.ini', $users);
               header('Location: ./');
               exit;
            } else {
               $this->view->alertName->attribute('class')->del('d-none');
            }
         }else{
            $this->view->alert->attribute('class')->del('d-none');
         }
      }
      $this->view->data->attribute('max')->set(date('Y-m-d'));
      $this->view->replaceStr('name', ifset_recursive($users,'',$PC,'name'));
      $this->view->replaceStr('pc',$PC);
      return $this->view;
   }
}

?>
