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
      foreach ($users as $key => &$value)
      {
         if(issets($value, 'start'))
         {
            $startTime = strtotime($value['start']);
            $value['start'] = DateToStr($startTime);

            if (issets($value, 'end'))
            {
               $endTime = strtotime($value['end']);
               $totalTime = $endTime - $startTime;
               $value['total'] = DateToStr($totalTime);
               $value['end'] = DateToStr($endTime);
            } else {
               $value['total'] = '';
               $value['end'] = '';
            }
         } else {
            $value['start'] = '';
            $value['total'] = '';
            $value['end'] = '';
         }
         $value['pc'] = $key;
         $value['progress'] = ifset($value,'progress','Desconhecido');
      }

      usort(
         $users,
         function($a, $b)
         {
            if (empty($b['total']))
               $retval = 0;
            else
               $retval = strtotime($a['total']) <=> strtotime($b['total']);
            if ($retval == 0)
            {
               $retval = $a['name'] <=> $b['name'];
            }
            return $retval;
         }
      );
      $count = 1;
      foreach ($users as &$value)
      {
         $value['ranking'] = $count;
         $count++;
      }

      $this->view->row->foreachBlocks($users);
      return $this->view;
   }
}

?>
