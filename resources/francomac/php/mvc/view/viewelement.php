<?php
namespace FrancoMAC\MVC\View;

require_once(__DIR__.'/../base.php');
use FrancoMAC\MVC\TBase;

require_once(__DIR__.'/viewattribute.php');
use FrancoMAC\MVC\View\TViewAttribute;

use DOMNode;
use DOMElement;
use DOMDocument;
use Exception;

/**
 *   TViewElement
 *
 *   manipulate a properly formatted DOMElement
 */
class TViewElement extends TBase
{
   /**
    *   Pointer DOMElement
    *
    *   @var   DOMElement
    */
   private $XMLElement = NULL;

   /**
    *   TViewElement child list
    *
    *   @var   TViewElement
    */
   private $ViewElementList = array();

   /**
    *   Options clean Elements
    *   @var   String
    */
   private $BlockOptions = array();

   /**
    *   Construct TViewElement
    *
    *   @method   __construct
    *   @param    DOMElement    $XMLElement
    */
   public function __construct(DOMElement $XMLElement)
   {
      $this->XMLElement = $XMLElement;
      if ($XMLElement->hasAttribute('viewBlock'))
      {
         $this->BlockOptions =
            explode(';', $XMLElement->getAttribute('viewBlock'));
      }
      $this->createElementList();
   }

   /**
    *   Gets the child or self element
    *
    *   @method   __get
    *   @param    string   $id
    *   @return   TViewElement
    */
   public function __get(string $id)
   {
      if ($id == 'self')
      {
         return $this;
      }

      $ViewElement = $this->getElement($id);
      if ($ViewElement)
      {
         return $ViewElement;
      }else{
         throw new Exception(
            "TView ERROR: $id not found"
         );
      }
   }

   /**
    *   Sets the child or self element
    *
    *   @method   __set
    *   @param    string   $id
    *   @param    TViewElement|
    *             TView|DOMNode|String|Numeric   $value
    *   @return   $value
    */
   public function __set(string $id, $value)
   {
      if ($id == 'self')
      {
         $ViewElement = $this;
      }else{
         $ViewElement = $this->getElement($id);
         if (!$ViewElement)
         {
            throw new Exception(
               "TView ERROR: $id not found"
            );
         }
      }
      $ViewElement->cleanMe();
      $ViewElement->insertOf($value);
      return $value;
   }

   /**
    *   Convert TViewElement to String
    *
    *   @method   __toString
    *   @return   string
    */
   public function __toString() : string
   {
      return $this->XMLElement->textContent;
   }

   /**
    *   Get attribute to element
    *   @method   attribute
    *   @param    string           $attribute
    *   @return   TViewAttribute
    */
   public function attribute(string $attribute) : TViewAttribute
   {
      if (!empty($attribute))
      {
         return new TViewAttribute($this->XMLElement,$attribute);
      }else{
         throw new Exception(
            'TView ERROR: Attribute name can not be empty'
         );
      }
   }

   /**
    *   Checks if an attribute exists
    *
    *   @method   hasAttribute
    *   @param    string  $attribute
    *   @return   bool
    */
   public function hasAttribute(string $attribute) : bool
   {
      return $this->XMLElement->hasAttribute($attribute);
   }

   /**
    *   Check if child exists
    *
    *   @method   hasElement
    *   @param    string       $id
    *   @return   bool
    */
   public function hasElement(string $id) : bool
   {
      return array_key_exists($id,$this->ViewElementList);
   }

   /**
    *   get DOMElement
    *
    *   @method   getXMLElement
    *   @return   DOMElement
    */
   public function getXMLElement() : DOMElement
   {
      return $this->XMLElement;
   }

   /**
    *   Deletes the element itself and its children
    *
    *   @method   deleteMe
    */
   public function deleteMe() : void
   {
      $this->XMLElement->parentNode
         ->removeChild($this->XMLElement);
   }

   /**
    *   Delete all your children
    *
    *   @method   cleanMe
    */
   public function cleanMe() : void
   {
      $count = $this->XMLElement->childNodes->count()-1;
      for ($i = $count; $i >= 0; $i--)
      {
         $value = $this->XMLElement->childNodes[$i];
         $value->parentNode->removeChild($value);
      }
   }

   /**
    *   Clone the element itself and its children
    *
    *   @method   cloneMe
    *   @return   TViewElement  The clone can
    *                           only be modified
    *                           by this return
    */
   public function cloneMe() : TViewElement
   {
      $XMLClone = $this->XMLElement->cloneNode(true);
      $XMLClone->removeAttribute('viewID');
      $XMLClone->removeAttribute('viewBlock');
      $this->XMLElement->parentNode->
         insertBefore($XMLClone, $this->XMLElement);
      return new TViewElement($XMLClone);
   }

   /**
    *   Inserts an element / text within the element itself
    *
    *   @method   insertOf
    *   @param    TViewAttribute|TViewElement|
    *             TView|DOMNode|String|Numeric     $valueIns
    *   @param    TViewAttribute|TViewElement|
    *             TView|DOMNode|String|Numeric     $nodeRef
    *             if null inserts as last child,
    *             if not, insert before the chosen element
    */
   public function insertOf($valueIns, $nodeRef = NULL) : void
   {
      $valueIns = $this->convertToDOM($valueIns);
      $nodeRef = ($nodeRef)?$nodeRef->getXMLElement():NULL;
      $this->insertElement(
         $valueIns,
         $this->XMLElement,
         $nodeRef);
      $this->createElementList($valueIns);
   }

   /**
    *   For all values in an array clone a block and replace
    *   the text of the element / attribute / child
    *
    *   @method   foreachBlocks
    *   @param    array           $array
    *             multi-dimensional array
    */
   public function foreachBlocks(array $array) : void
   {
      $XMLOldDoc = $this->XMLElement->ownerDocument;
      $XMLOldText = $XMLOldDoc->saveXML($this->XMLElement);

      $XMLNewDoc = new DOMDocument();
      $XMLNewDoc->preserveWhiteSpace = false;
      $XMLNewDoc->formatOutput = true;

      foreach ($array as $key => $arrayValues)
      {
         $XMLNewText = str_replace(
            '{$key}',
            $key,
            $XMLOldText);

         if (is_array($arrayValues))
         {
            $arrayKeys = array_keys($arrayValues);
            $twoDimensional = true;
         }else{
            $arrayKeys = array_keys($array);
            $twoDimensional = false;
         }

         $count = count($arrayKeys);
         for ($i=0; $i < $count; $i++)
         {
            $arrayKeys[$i] = '{'.$arrayKeys[$i].'}';
         }

         if ($twoDimensional)
         {
            $XMLNewText = str_replace(
               $arrayKeys,
               $arrayValues,
               $XMLNewText);
         }else{
            $XMLNewText = str_replace(
               '{$value}',
               $arrayValues,
               $XMLNewText);
         }

         $XMLNewDoc->loadXML($XMLNewText, $this->getXMLOptions());
         $XMLNewElement = $XMLOldDoc->importNode(
            $XMLNewDoc->documentElement, true);
         $XMLNewElement->removeAttribute('viewID');
         $this->cleanBlockElement($XMLNewElement);

         if (!is_null($XMLNewElement))
         {
            $this->XMLElement->parentNode->
               insertBefore($XMLNewElement,$this->XMLElement);
         }
      }
   }

   /**
    *   replace text in the element, its children and attributes
    *
    *   @method   replaceStr
    *   @param    string       $search
    *   @param    string       $replace
    */
   public function replaceStr(string $search, string $replace) : void
   {
      $search = '{'.$search.'}';
      $this->replaceElement($this->XMLElement, $search, $replace);
      $XMLElementList = $this->XMLElement->getElementsByTagName('*');
      foreach ($XMLElementList as $XMLElement)
      {
         $this->replaceElement($XMLElement, $search, $replace);
      }
   }

   /**
    *   clean optinos BlockElement
    *   @method   cleanBlockElement
    *   @param    DOMElement          $XMLNewElement
    */
   private function cleanBlockElement(DOMElement $XMLNewElement) : void
   {
      $XMLNewElement->removeAttribute('viewBlock');
      if (in_array('attributes', $this->BlockOptions))
      {
         foreach ($XMLNewElement->attributes as $value)
         {
            if (empty($value->value))
            {
               $XMLNewElement->removeAttribute($value->name);
            }
         }
      }

      if (in_array('nodes', $this->BlockOptions))
      {
         foreach ($XMLNewElement->childNodes as $value)
         {
            if (($value instanceof DOMElement)
            and (!$value->hasChildNodes()))
            {
               $XMLNewElement->removeChild($value);
            }
         }
      }

      if (in_array('self', $this->BlockOptions))
      {
         if (!$XMLNewElement->hasChildNodes())
         {
            $XMLNewElement->parentNode->removeChild($XMLNewElement);
            $XMLNewElement = NULL;
         }
      }
   }

   /**
    *   replace text in the element, its children and attributes
    *
    *   @method   replaceElement
    *   @param    DOMElement       $XMLElement
    *   @param    string           $search
    *   @param    string           $replace
    */
   private function replaceElement(DOMElement $XMLElement,
      string $search, string $replace) : void
   {
      foreach ($XMLElement->attributes as $attribute)
      {
         $attribute->nodeValue = str_replace(
            $search,
            $replace,
            $attribute->nodeValue);
      }

      foreach ($XMLElement->childNodes as $child)
      {
         if ($child->nodeType == XML_TEXT_NODE)
         {
            $child->nodeValue = str_replace(
               $search,
               $replace,
               $child->nodeValue);
         }
      }
   }

   /**
    *   insert Element in Element
    *
    *   @method   insertElement
    *   @param    DOMNode         $from
    *   @param    DOMNode         $to
    *   @param    DOMNode         $Before
    *             if null inserts as last child,
    *             if not, insert before the chosen element
    */
   private function insertElement(DOMNode $from,
      DOMNode $to, DOMNode $Before = NULL) : void
   {
      if ($from->ownerDocument !== $to->ownerDocument)
      {
         $from = $to->ownerDocument
            ->importNode($from, true);
      }

      if (is_null($Before))
      {
         $to->appendChild($from);
      }else{
         $to->insertBefore($from, $Before);
      }
   }

   /**
    *   Convert Types to DOMNode
    *
    *   @method   convertToDOM
    *   @param    TViewAttribute|TViewElement|
    *             TView|DOMNode|String|Numeric    $value
    *   @return   DOMNode|NULL
    */
   private function convertToDOM($value) : ?DOMNode
   {
      if( (is_numeric($value)) OR (is_string($value)))
      {
         return $this->XMLElement->ownerDocument
            ->createTextNode($value);
      }

      if ($value instanceof TView)
      {
         return $value->getXMLDocument()->documentElement;
      }

      if ($value instanceof TViewElement)
      {
         return $value->getXMLElement();
      }

      if ($value instanceof DOMNode)
      {
         return $value;
      }

      throw new Exception(
         'TView ERROR: this type is not supported'
      );
   }

   /**
    *   Create Element List Childs
    *
    *   @method   createElementList
    */
   private function createElementList() : void
   {
      $this->ViewElementList = array();
      $XMLElementList = $this->XMLElement->getElementsByTagName('*');
      foreach ($XMLElementList as $XMLElement)
      {
         if($XMLElement->hasAttribute('viewID'))
         {
            $this->ViewElementList[
               $XMLElement->getAttribute('viewID')] =
                  new TViewElement($XMLElement);
         }
      }
   }

   /**
    *   get Element by viewID
    *
    *   @method   getElement
    *   @param    string       $id
    *   @return   TViewElement|NULL
    */
   private function getElement(string $id) : ?TViewElement
   {
      if (array_key_exists($id,$this->ViewElementList))
      {
         return $this->ViewElementList[$id];
      }else{
         return NULL;
      }
   }

   /**
    *   Create configurate type
    *
    *   @method   getXMLOptions
    *   @return   int
    */
   protected function getXMLOptions() : int
   {
      return
         LIBXML_BIGLINES | //Permite que números de linha maiores que 65535 sejam informados corretamente.
         //LIBXML_COMPACT | //compacta nodes
         //LIBXML_DTDATTR | //Padrão de atributos DTD
         //LIBXML_DTDLOAD | //Carrega o subset externo
         //LIBXML_DTDVALID | //Valida com o DTD
         //LIBXML_NOBLANKS| //exclue nodes em branco
         //LIBXML_NOCDATA | //Fundi CDATA com text nodes
         //LIBXML_NOEMPTYTAG | //expande nodes <br/> para <br></br>
         //LIBXML_NOENT | //Substitue entidades
         //LIBXML_NOERROR| //suprime erros
         //LIBXML_NONET | //Desabilita o acesso a rede quando carregando documentos
         //LIBXML_NOWARNING | //suprime avisos
         LIBXML_NOXMLDECL | //exclue declarações cabeçalho
         //LIBXML_NSCLEAN | //remove declarações redundantes namespace
         //LIBXML_XINCLUDE | //Implementa substituições XInclude
         //LIBXML_ERR_ERROR | //recupera o erros
         //LIBXML_ERR_FATAL | //erro fatal
         //LIBXML_ERR_NONE | //sem erros
         //LIBXML_ERR_WARNING | //simples aviso
         //LIBXML_VERSION | //Versão da libxml como 20605 ou 20617
         //LIBXML_DOTTED_VERSION | //Versão da libxml como 2.6.5 ou 2.6.17
         //LIBXML_SCHEMA_CREATE | //Criar nós de valor padrão / fixo durante a validação do esquema XSD
         //LIBXML_HTML_NOIMPLIED | //Define o sinalizador HTML_PARSE_NOIMPLIED, que desativa a adição automática de elementos implícitos de html / body ....
         //LIBXML_HTML_NODEFDTD | //Define o sinalizador HTML_PARSE_NODEFDTD, que impede que um tipo de documento padrão seja adicionado quando um não é encontrado.
         0;
   }
}
?>
