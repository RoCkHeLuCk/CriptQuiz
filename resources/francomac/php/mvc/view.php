<?php
namespace FrancoMAC\MVC;
require_once(__DIR__.'/view/viewelement.php');
use FrancoMAC\MVC\View\TViewElement;
use DOMDocument;
use DOMXpath;
use Exception;

libxml_use_internal_errors(true);

/**
 *   TView
 *
 *  Manipulate DOMDocument from TView
 */
class TView extends TViewElement
{
   /**
    *   Pointer DOMDocument
    *
    *   @var   DOMDocument
    */
   private $XMLDoc = NULL;

   /**
    *   variables to be inserted in the document after
    *   the end of the manipulation and before being saved.
    *
    *   @var   object/array/string/numeric
    */
   private $varList = array();

   /**
    *   Construct TView
    *
    *   @method   __construct
    *   @param    bool       $formated   XML formatted
    *   @param    string        $encoding   encode type
    */
   public function __construct(bool $formated = true,
      string $encoding = 'UTF-8')
   {
      libxml_clear_errors();
      $this->XMLDoc = new DOMDocument('1.0', $encoding);
      $this->XMLDoc->preserveWhiteSpace = false;
      $this->XMLDoc->formatOutput = $formated;
      $this->XMLDoc->validateOnParse = true;
   }

   /**
    *   get DOMDocument
    *
    *   @method   getXMLDocument
    *   @return   DOMDocument
    */
   public function getXMLDocument() : DOMDocument
   {
      return $this->XMLDoc;
   }

   /**
    *   Convert document to String
    *
    *   @method   __toString
    *   @return   string
    */
   public function __toString() : string
   {
      return $this->XMLDoc->textContent;
   }

   /**
    *   Loat from XML Text
    *
    *   @method   loadText
    *   @param    string     $text
    */
   public function loadText(string $text) : void
   {
      $text = preg_replace('/<!---.*?--->/smi', '', $text);
      $this->XMLDoc->loadXML($text, $this->getXMLOptions());
      $this->Error();

      TViewElement::__construct($this->XMLDoc->documentElement);
   }

   /**
    *   Load from XML File
    *
    *   @method   loadFile
    *   @param    string     $filename
    */
   public function loadFile(string $filename) : void
   {
      if (!file_exists($filename))
      {
         throw new Exception(
            "TView ERROR: File ($filename) no found."
         );
      }
      $this->loadText(file_get_contents($filename));
   }

   /**
    *   Save to XML text
    *
    *   @method   saveText
    *   @param    bool       $clean    clears any unmanipulated or
    *                                  empty blocks\variables\text
    *   @return   string
    */
   public function saveText(bool $clean = true) : string
   {
      if ($clean)
      {
         $this->cleanElementsDelMe();
      }
      $XMLText = $this->XMLDoc->saveXML( NULL, $this->getXMLOptions());
      $this->replaceVariables($XMLText);
      if ($clean)
      {
         $XMLText = preg_replace('/\{\$*\w*\}/', '', $XMLText);
         $XMLText = preg_replace('/ viewID=[\"\']\w*[\"\']/', '', $XMLText);
      }
      return $XMLText;
   }

   /**
    *   save to XML file
    *
    *   @method   saveFile
    *   @param    string     $fileName
    *   @param    bool       $clean    clears any unmanipulated or
    *                                  empty blocks\variables\text
    */
   public function saveFile(string $fileName, bool $clean) : void
   {
      $XMLText = $this->saveText($clean);
      file_put_contents($filename , $XMLText, FILE_TEXT);
   }

   /**
    *   set variable
    *
    *   @method   setVariable
    *   @param    string        $name       Name the variable
    *   @param    Object/array
    *             /string/numeric $variable
    */
   public function setVariable(string $name, $variable) : void
   {
      $this->varList[$name] = $variable;
   }

   /**
    *   replaces all variables within the document
    *
    *   @method   replaceVariables
    *   @param    string             $text
    */
   private function replaceVariables(string &$text) : void
   {
      if ($this->varList)
      {
         $text = preg_replace_callback(
            '/\{\$(\w+)(?(?=\.)\.(\w+))\}/',
            function ($match)
            {
               if (array_key_exists($match[1],$this->varList))
               {
                  $Obj = $this->varList[$match[1]];
                  if(isset($match[2]))
                  {
                     $var = $match[2];
                     if(is_array($Obj))
                     {
                        return $Obj[$var];
                     }
                     if (is_object($Obj))
                     {
                        return $Obj->$var;
                     }
                  }
                  return $Obj;
               }
               return '';
            },
            $text
         );
      }
   }

   /**
    *   clean empty Blocks
    *
    *   @method   cleanElementsDelMe
    */
   private function cleanElementsDelMe() : void
   {
      $XMLXPath = new DOMXpath($this->XMLDoc);
      $this->XMLDoc->normalizeDocument();

      $XMLElementList = $XMLXPath->query('//*[@viewBlock]');
      foreach ($XMLElementList as $XMLElement)
      {
         $XMLElement->parentNode->removeChild($XMLElement);
      }
   }

   /**
    *   show error message libxml
    *   @method   Error
    */
   private function Error() : void
   {
      $msg = '';
      foreach(libxml_get_errors() as $e)
      {
         switch ($e->code)
         {
            case 57:
            case 522:
            break;
            default:
            {
               $msg .= ' TView ERROR: '.$e->message;
            } break;
         }
      }
      if (!empty($msg))
      {
         throw new Exception($msg);
      }
      libxml_clear_errors();
   }
}
?>
