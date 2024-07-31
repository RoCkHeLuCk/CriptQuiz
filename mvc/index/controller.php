<?php
namespace Index;
//require_once('model.php');
use FrancoMAC\MVC\TController;
use FrancoMAC\MVC\TView;
use FrancoMAC\Extras\TRequest;

class Controller extends TController
{
   private $view = NULL;
   private $request = NULL;

   public function __construct()
   {
      //$this->setDebug(false);
      //$this->model = new Model();
      $this->view = new TView();
      $this->view->loadFile(__DIR__.'/view/index.html');
      $this->request = new TRequest();
      $this->request->add('PG', TRequest::IN_GET);
   }

   public function execute()
   {
      $this->view->setVariable('title', 'Decrypt Quiz');
      $clone = $this->view->css->cloneMe();
      $clone->attribute('href')
         ->set('./resources/bootstrap/css/bootstrap.min.css');
      $clone = $this->view->css->cloneMe();
      $clone->attribute('href')
         ->set('./mvc/index/view/ajust.css');

      $this->main();
      $this->menu();
      echo $this->view->saveText();
   }

   public function main()
   {
      if($this->request->PG)
      switch ($this->request->PG)
      {
         case 'Login':
         {
            require_once(__DIR__.'\..\login\controller.php');
            $login = new \Login\Controller();
            $this->view->main->insertOf($login->execute());
         }break;

         case 'Logout':
         {
            $_SESSION = array();
            header('Location: ./');
            exit;
         }break;

         case 'Ranking': {
               require_once(__DIR__ . '\..\ranking\controller.php');
               $test = new \Ranking\Controller();
               $this->view->main->insertOf($test->execute());
         }break;

         case 'Quiz':
         {
            require_once(__DIR__.'\..\quiz\controller.php');
            $test = new \Quiz\Controller();
            $this->view->main->insertOf($test->execute());
         }break;

         case 'Test':
         {
            require_once(__DIR__.'\..\test\controller.php');
            $test = new \Test\Controller();
            $this->view->main->insertOf($test->execute());
         }break;

         case 'System':
         {
            require_once(__DIR__.'\..\system\controller.php');
            $test = new \System\Controller();
            $this->view->main->insertOf($test->execute());
         }break;

         case 'Lock':
         {
            require_once(__DIR__.'\..\lock\controller.php');
            $test = new \Lock\Controller();
            $this->view->main->insertOf($test->execute());
         }break;

         default:
         {
            $E404 = new TView();
            $E404->loadText('<img src="image/404.png" width="400"
               height="400" alt="Error 404"></img>');
            $this->view->main = $E404;
         }break;
      }
   }

   public function menu()
   {
      if (issets($_SESSION,'nome','senha'))
      {
         $clone = $this->view->menuitem->cloneMe();
         $clone->attribute('href')->set('?PG=Logout');
         $clone->insertOf('Sair');
         $clone->menuimage->attribute('src')->set('./image/btn_exit.svg');

         $clone = $this->view->menuitem->cloneMe();
         $clone->attribute('href')->set('?PG=Quiz&Q=0');
         $clone->insertOf('Quiz');
         $clone->menuimage->attribute('src')->set('./image/btn_test.svg');

         $clone = $this->view->menuitem->cloneMe();
         $clone->attribute('href')->set('?PG=Test');
         $clone->insertOf('Hackear o Sistema');
         $clone->menuimage->attribute('src')->set('./image/btn_secure.svg');
      }else{
         $clone = $this->view->menuitem->cloneMe();
         $clone->attribute('href')->set('?PG=Login');
         $clone->insertOf('Entrar');
         $clone->menuimage->attribute('src')->set('./image/btn_login.svg');
      }

      $clone = $this->view->menuitem->cloneMe();
      $clone->attribute('href')->set('?PG=Ranking');
      $clone->insertOf('Progresso');
      $clone->menuimage->attribute('src')->set('./image/btn_ranking.svg');

   }

}
?>
