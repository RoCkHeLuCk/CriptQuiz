<?php
namespace FrancoMAC\MVC\View;
use DOMElement;
use DOMAttr;

/**
 *   TViewAttribute
 *
 *   Manipulates the attributes of an TViewElement
 */
class TViewAttribute
{
   /**
    *   Pointer DOMAttr to DOMElement
    *
    *   @var DOMElement
    */
   private $XMLownerElement = NULL;

   /**
    *   Name to attribute
    *
    *   @var   string
    */
   private $AttName = NULL;

   /**
    *   Contruct TViewAttribute
    *
    *   @method   __construct
    *   @param    DOMElement       $XMLownerElement
    *   @param    string           $AttName
    */
   public function __construct(DOMElement $XMLownerElement,
      string $AttName)
   {
      $this->XMLownerElement = $XMLownerElement;
      $this->AttName = $AttName;

      if (!$this->XMLownerElement->hasAttribute($AttName))
      {
         $this->XMLownerElement->setAttribute($AttName,'');
      }
   }

   /**
    *   Convert to String
    *
    *   @method   __toString
    *   @return   string
    */
   public function __toString() : string
   {
      return $this->get();
   }

   /**
    *   Get DOMAttr
    *
    *   @method   getDOMAttr
    *   @return   DOMAttr
    */
   public function getDOMAttr() : DOMAttr
   {
      return $this->XMLownerElement->getAttributeNode($this->AttName);
   }

   /**
    *   Set text to attribute
    *
    *   @method   set
    *   @param    string   $value
    */
   public function set(string $value)
   {
      $this->XMLownerElement->setAttribute($this->AttName,
         superTrim($value));
   }

   /**
    *   get attribute values
    *
    *   @method   get
    *   @return   string
    */
   public function get()
   {
      return $this->XMLownerElement->getAttribute($this->AttName);
   }

   /**
    *   Add text to attribute
    *
    *   @method   add
    *   @param    string   $value
    */
   public function add(string $value) : void
   {
      $novo = $this->get().' '.$value;
      $this->set($novo);
   }

   /**
    *   Detele text from attribute
    *
    *   @method   del
    *   @param    string   $value
    */
   public function del(string $value) : void
   {
      $novo = str_replace($value,'',$this->get());
      $this->set($novo);
   }

   /**
    *   Checks whether a text exists
    *
    *   @method   has
    *   @param    string    $value
    *   @return   bool
    */
   public function has(string $value) : bool
   {
      return (stripos($this->get(),$value) === true);
   }

   /**
    *   Alternates between the existence text in attribute
    *
    *   @method   toggle
    *   @param    string   $value
    */
   public function toggle(string $value) : void
   {
      if (stripos($this->get(),$value) === false)
      {
         $this->add($value);
      }else{
         $this->del($value);
      }
   }

   /**
    *   Deletes the attribute itself
    *
    *   @method   deleteMe
    */
   public function deleteMe() : void
   {
      $this->XMLownerElement->removeAttribute($this->AttName);
   }
}

?>
