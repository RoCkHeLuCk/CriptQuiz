<?php
namespace FrancoMAC\MVC;
require_once(__DIR__.'/base.php');
use FrancoMAC\MVC\TBase;

use PDO;
use PDOException;
use Exception;
/**
 *   TModel
 *
 *   Class for connection and manipulation in the database
 */
class TModel extends TBase
{
   /**
    *   List Data Base connection
    *
    *   @var   PDO
    */
   private static $dataBase = array();

   /**
    *   Error message
    *
    *   @var   string
    */
   private $errorMessage = '';

   /**
    *   Construc TModel connected DataBase PDO
    *
    *   @method   connectDB
    *   @param    string      $fileName    INI File formatted
    */
   protected function connectDB(string $fileName)
   {
		if (!file_exists($fileName))
		{
         throw new Exception(
            "TModel ERROR: File ($fileName) no found."
         );
      }

      TModel::$dataBase = array();
      $iniFile = parse_ini_file($fileName, true);

      foreach ($iniFile as $key => $value)
      {
         $dsn = $value['driver'].':host='.$value['host'];

         if ( array_key_exists('port',$value) )
         {
            $dsn .= ';port='.$value['port'];
         }

         if ( array_key_exists('name',$value) )
         {
            $dsn .= ';dbname='.$value['name'];
         }

         try
         {
            TModel::$dataBase[$key] = new PDO(
               $dsn,
               $value['user'],
               $value['password']
            );
            TModel::$dataBase[$key]->setAttribute(
               PDO::ATTR_ERRMODE,
               PDO::ERRMODE_EXCEPTION
            );
   		} catch (PDOException $e) {
            throw new Exception(
               'TModel ERROR: Connection Error: '.$e->getMessage()
            );
   		}
      }
   }

   /**
    *   __get PDO
    *   @method   __get
    *   @param    string   $id
    *   @return   PDO
    */
   public function __get(string $id) : PDO
   {
      if (array_key_exists($id,TModel::$dataBase))
      {
         return TModel::$dataBase[$id];
      }else{
         throw new Exception("TModel Error: $id no found.");
      }
   }

   /**
    *   select query
    *   @method   select
    *   @param    string    $id
    *   @param    string    $sql
    *   @param    int       $style
    *   @param    boolean   $Error
    *   @return   array
    */
   protected function select(string $id, string $sql,
      int $style = PDO::FETCH_ASSOC, bool $error = false) : array
   {
      try
      {
         $result = TModel::__get($id)->query($sql);
      } catch (PDOException $e) {
         $this->errorMessage = $e->getMessage();
         if ($error)
         {
            throw new Exception($this->errorMessage);
         }
         return array();
      }
      if ( ($result) AND ($result->rowCount() > 0) )
      {
         return $result->fetchAll($style);
      }
      return array();
   }

   /**
    *   test query
    *
    *   @method   test
    *   @param    string    $id
    *   @param    string    $sql
    *   @param    boolean   $Error
    *   @return   bool
    */
   protected function test(string $id, string $sql,
      bool $error = false) : bool
   {
      try
      {
          $result = TModel::__get($id)->query($sql);
      } catch (PDOException $e) {
         $this->errorMessage = $e->getMessage();
         if ($error)
         {
            throw new Exception($this->errorMessage);
         }
         return false;
      }
      return ( $result == true );
   }

   /**
    *   get error message the last select/test
    *
    *   @method   getErrorMessage
    *   @return   string
    */
   public function getErrorMessage() : string
   {
      return $this->errorMessage;
   }

   /**
    *   convert Date to DB Date
    *
    *   @method   convertDateToBD
    *   @param    string            $date
    *   @return   string
    */
   protected function convertDateToBD(string $date) : string
   {
      if( !empty($date) AND (strpos($date,"/")) )
      {
         $d = explode("/",$date);
         return ($d[2]."-".$d[1]."-".$d[0]);
      }else{
         return '';
      }
   }

   /**
    *   convert Date DB to Date
    *
    *   @method   convertDateOfBD
    *   @param    string            $date
    *   @return   string
    */
   protected function convertDateOfBD(string $date) : string
   {
      if( !empty($date) AND (strpos($date,"-")) )
      {
         $d = explode("-",$date);
         return ($d[2]."/".$d[1]."/".$d[0]);
      }else{
         return '';
      }
   }
}
?>
