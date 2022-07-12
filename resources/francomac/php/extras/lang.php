<?php
namespace FrancoMAC\Extras;

require_once(__DIR__.'/../mvc/base.php');
use FrancoMAC\MVC\TBase;
use Exception;

/**
 *   TLang
 *   Loads the files for translation either
 *   automatically or configuring.
 */
class TLang extends TBase
{
   /**
    *   Directory of Languages
    *   @var   [type]
    */
   private $path = '';

   /**
    *   List Language INI File
    *   @var   array
    */
   private $langList = array();

   /**
    *   List words of translate
    *   @var   array
    */
   public $langTran = array();

   /**
    *   Construct class
    *
    *   @method   __construct
    *   @param    string        $path   Directory Language
    */
   public function __construct(string $path)
   {
      $fileList = $path . '/list.ini';
      if (!file_exists($fileList))
      {
         throw new Exception(
            "TLang ERROR: File ($fileList) dont found"
         );
      }else{
         $this->langList = parse_ini_file($fileList, true);
         $this->path = $path;
      }
   }

   /**
    *   Return Language List from INI File
    *
    *   @method   getLanguageList
    *   @return   array
    */
   public function getLanguageList() : array
   {
      if (!$this->langList)
      {
         $this->setLanguage();
      }
      return $this->langList;
   }

   /**
    *   set Language from translate,
    *   Blank Bears System Configuration
    *
    *   @method   setLanguage
    *   @param    string        $language
    */
   public function setLanguage(string $language = '') : void
   {
      if (empty($language))
      {
         $this->loadLanguage( $this->getSystemLang() );
      }else{
         if (array_key_exists($language, $this->langList))
         {
            $this->loadLanguage($language);
         }else{
            throw new Exception(
               "TLang ERROR: Language ($language) dont found"
            );
         }
      }
   }

   /**
    *   Get the word translated
    *
    *   @method   __get
    *   @param    string   $key
    *   @return   string
    */
   public function __get(string $key) : string
   {
      if (array_key_exists($key,$this->langTran))
      {
         return $this->langTran[$key];
      }else{
         return "TLang ERROR: $key don't exist";
      }
   }

   /**
    *   Get first language of the system that has translation.
    *
    *   @method   getSystemLang
    *   @return   string
    */
   private function getSystemLang() : string
   {
      $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

      foreach ($lang as $value)
      {
         $value = strtolower($value);
         if (array_key_exists($value, $this->langList))
         {
            return $value;
         }
      }
      return '';
   }

   /**
    *   loads the list of words to be translated
    *
    *   @method   loadLanguage
    *   @param    string         $language
    */
   private function loadLanguage(string $language) : void
   {
      if (($this->langList)
      AND(isset($this->langList[$language]))
      AND(isset($this->langList[$language]['filename'])))
      {
         $fileName = $this->path.'/'.
            $this->langList[$language]['filename'];
         if(file_exists($fileName))
         {
            $this->langTran = require($fileName);
         }else{
            throw new Exception(
               "TLang ERROR: File ($fileName) dont found"
            );
         }
      }else{
         throw new Exception(
            'TLang ERROR: Malformed list of languages'
         );
      }
   }
}
?>
