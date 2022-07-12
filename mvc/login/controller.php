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

      if ($_POST)
      {
         $data = explode('-',$this->request->data);
         $senha = $data[0]+$data[1]+$data[2]+$PC;

         if ($this->request->senha == $senha)
         {
            $_SESSION = array();
            $_SESSION['nome'] = $this->request->nome;
            $_SESSION['data'] = $data;
            $_SESSION['senha'] = $this->request->senha;
            $_SESSION['pc'] = $PC;
            $_SESSION['tempo'] = time();
            header('Location: ./');
            exit;
         }else{
            $this->view->alert->attribute('class')->del('d-none');
         }
      }
      $this->view->data->attribute('max')->set(date('Y-m-d'));
      $this->view->dica->replaceStr('pc',$PC);
      return $this->view;
   }
}

?>
