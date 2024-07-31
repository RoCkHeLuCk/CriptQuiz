<?php
namespace Ranking;
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
      $this->view->loadFile(__DIR__.'/view/ranking.html');
      $users = parse_ini_file(__DIR__.'/../../users.ini', true);
      $count = 0;
      foreach ($users as $key => &$value)
      {
         if(issets($value, 'start'))
         {
            $startTime = strtotime($value['start']);
            $endTime = (issets($value,'end'))?strtotime($value['end']): strtotime(time());
            $progressTime = $endTime - $startTime;
            $totalTime = $endTime - $startTime;
            $value['start'] = DateToStr($startTime);
            $value['end'] = DateToStr($endTime);
            $value['progress'] = DateToStr($progressTime);
            $value['total'] = DateToStr($totalTime);
         }

         $rowClone = $this->view->row->cloneMe();
         $colClone = $rowClone->col->cloneMe();
         $colClone->insertOf($count);
         $colClone = $rowClone->col->cloneMe();
         $colClone->insertOf($key);
         foreach ($value as $value2)
         {
            $colClone = $rowClone->col->cloneMe();
            $colClone->insertOf($value2);
         }
         $count++;
      }
      return $this->view;
   }
}

?>
