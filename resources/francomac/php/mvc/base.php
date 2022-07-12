<?php
namespace FrancoMAC\MVC;

class TBase
{
   /**
    *   Define DEBUG
    *   @var   bool
    */
   private static $DEBUG = true;

   /**
    *   Define Email adminstrator
    *
    *   @var   string
    */
   private static $ADMIMMAIL = NULL;

   /**
    *   Define Subject Email ERROR
    *
    *   @var   string
    */
   private static $SUBJECTMAIL = NULL;

   /**
    *   Set enable DEBUG
    *
    *   @method   setDebug
    *   @param    bool       $enabled
    */
   protected function setDebug(bool $enabled) : void
   {
      TBase::$DEBUG = $enabled;
      if ( $enabled )
      {
         error_reporting(E_ALL);
      	ini_set("display_errors", true);
      	ini_set("html_errors", true);
      }else{
      	error_reporting(0);
      	ini_set("display_errors", false);
      	ini_set("html_errors", false);
      }
   }

   /**
    *   Set Admin email and subject error
    *
    *   @method   setAdminEmail
    *   @param    string          $adminEmail
    *   @param    string          $subject
    */
   protected function setAdminEmail(string $adminEmail, string $subject) : void
   {
      TBase::$ADMIMMAIL = $adminEmail;
      TBase::$SUBJECTMAIL = $subject;
   }

   /**
    *   Checks if DEBUG is defined and if it is true
    *
    *   @method   isDebug
    *   @return   bool
    */
   protected function isDebug() : bool
   {
      return TBase::$DEBUG;
   }

   /**
    *   Sent to the adminstrator's email.
    *
    *   @method   ERROR
    *   @param    string   $message
    */
   protected function sendMail(string $message) : void
   {
      if ((TBase::$ADMIMMAIL)AND(TBase::$SUBJECTMAIL))
      {
         $message = wordwrap($message, 70);
         mail(TBase::$ADMIMMAIL, TBase::$SUBJECTMAIL, $message);
      }
   }
}
?>
