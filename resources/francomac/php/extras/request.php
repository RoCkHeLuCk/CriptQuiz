<?php
namespace FrancoMAC\Extras;

require_once(__DIR__.'/../mvc/base.php');
use FrancoMAC\MVC\TBase;
use Exception;
/**
 *   Element Request manipulation
 */
class TRequestElement
{
   /**
    *   Cookie 31/12/2038
    *
    *   @var   int
    */
   private const COOKIE_2038 = 2147483647;

   /**
    *   array key is cookie
    *
    *   @var   string
    */
   private $cookieKey = '';

   /**
    *   name var in out
    *
    *   @var   string
    */
   private $name = '';

   /**
    *   value var in out
    *
    *   @var   string|numeric
    */
   private $value = '';

   /**
    *   bitwise options IO
    *
    *   @var   int
    */
   private $ioBitwise = 0;

   /**
    *   function call validate input
    *
    *   @var   callable
    */
   private $validate = NULL;

   /**
    *   construc Request Element
    *
    *   @method   __construct
    *   @param    string        $name
    *   @param    string        $cookieKey
    *   @param    int           $ioBitwise
    *   @param    callable      $validate
    */
   public function __construct(string $name, string $cookieKey = '',
      int $ioBitwise = 0, ?callable $validate = NULL)
   {
      $this->name = $name;
      $this->cookieKey = empty($cookieKey)?$name:"$cookieKey[$name]";
      $this->ioBitwise = $ioBitwise;
      $this->validate = $validate;

      if ((1 & $ioBitwise)AND(isset($_POST[$name])))
      {
         $this->value = $_POST[$name];
      }else{
         if ((2 & $ioBitwise)AND(isset($_GET[$name])))
         {
            $this->value = $_GET[$name];
         }else{
            if ((4 & $ioBitwise)AND(isset($_COOKIE[$name])))
            {
               $this->value = $_COOKIE[$name];
            }
         }
      }

      $this->valid();
      $this->saveCookie();
   }

   /**
    *   get value Element
    *
    *   @method   getValue
    *   @return   string
    */
   public function getValue() : string
   {
      return $this->value;
   }

   /**
    *   Set value Element
    *
    *   @method   setValue
    *   @param    string     $value
    */
   public function setValue(string $value) : void
   {
      $this->value = $value;
      $this->valid();
      $this->saveCookie();
   }

   /**
    *   is out URL
    *
    *   @method   isURL
    *   @return   bool
    */
   public function isURL() : bool
   {
      return (8 & $this->ioBitwise);
   }

   /**
    *   validate value in
    *
    *   @method   valid
    */
   private function valid() : void
   {
      if ($this->validate)
      {
         $call = $this->validate;
         $this->value =  $call($this->value);
      }
   }

   /**
    *   save cookie
    *
    *   @method   saveCookie
    */
   private function saveCookie() : void
   {
      if (16 & $this->ioBitwise)
      {
         setcookie($this->cookieKey,
            $this->value,
            self::COOKIE_2038);
      }
   }
}

/**
 *   Request class manipulation Request/save cookie
 */
class TRequest extends TBase
{
   /**
    *   consts bitwise options from Elements
    *
    *   @var   int
    */
   public const IN_POST = 1;      //000001
   public const IN_GET = 2;       //000010
   public const IN_COOKIE = 4;    //000100
   public const IN_REQUEST = 7;   //000111
   public const OUT_URL = 8;      //001000
   public const OUT_COOKIE = 16;  //010000

   /**
    *   Request Element List
    *
    *   @var   array
    */
   private $requestList = array();

   /**
    *   Cookie Key array separator
    *
    *   @var   string
    */
   private $cookieKey = '';

   /**
    *   construc Request Controller
    *
    *   @method   __construct
    *   @param    string        $cookieKey
    */
   public function __construct(string $cookieKey = '')
   {
      $this->cookieKey = $cookieKey;
   }

   /**
    *   add Element Request controller
    *
    *   @method   add
    *   @param    string   $name
    *   @param    int      $ioBitwise
    *   @param    [type]   $validate
    */
   public function add(string $name, int $ioBitwise,
      ?callable $validate = NULL) : void
   {
      $this->requestList[$name] = new TRequestElement($name,
         $this->cookieKey, $ioBitwise, $validate);
   }

   /**
    *   get Element value
    *
    *   @method   __get
    *   @param    string   $key
    *   @return   string
    */
   public function __get(string $key)
   {
      if (array_key_exists($key, $this->requestList))
      {
         return $this->requestList[$key]->getValue();
      }else{
         throw new Exception(
            "TRequest ERROR: Element ($key) no found"
         );
      }
   }

   /**
    *   set Element value
    *
    *   @method   __set
    *   @param    string   $key
    *   @param    string   $value
    */
   public function __set(string $key, string $value)
   {
      if (array_key_exists($key, $this->requestList))
      {
         $this->requestList[$key]->setValue($value);
      }else{
         throw new Exception(
            "TRequest ERROR: Element ($key) no found"
         );
      }
      return $value;
   }

   /**
    *   parsed Url encoding
    *
    *   @method   parseURL
    *   @return   string
    */
   public function parseURL() : string
   {
      $result = array();
      foreach ($this->requestList as $key => $value)
      {
         if ($value->isURL())
         {
            $result[$key] = $value->getValue();
         }
      }
      return './?'.http_build_query($result);
   }

   /**
    *   Header url
    *   @method   Header
    */
   public function Header() : void
   {
      header("Location: ".$this->parseURL());
   }
}
?>
