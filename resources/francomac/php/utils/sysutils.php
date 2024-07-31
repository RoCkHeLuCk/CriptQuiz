<?php

/**
*   remove all double spaces, whitespace of all text.
*
*   @method   superTrim
*   @param    string      $text
*   @return   string
*/
function superTrim(string $text) : string
{
   return trim(preg_replace('/\s+/',' ', $text));
}

/**
 *   Remove Accents all text
 *   @method   removeAccents
 *   @param    string          $value
 *   @return   string
 */
function removeAccents(string $value) : string
{
    return preg_replace(
      array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/","/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/",
            "/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),
            explode(" ","a A e E i I o O u U n N c C"),
            $value);
}

/**
 *   tests if multiple elements of the same level exist
 *   in an array
 *
 *   @method   issets
 *   @param    array   $array
 *   @param    string   $keys
 *   @return   bool
 */
function issets(?array $array, string ...$keys) : bool
{
   if ((!$array) OR (!$keys))
   {
      return false;
   }

   foreach ($keys as $key)
   {
      if (!array_key_exists($key,$array))
      {
         return false;
      }
   }

   return true;
}

/**
 *   test if an element in element, recursive an array exists
 *
 *   @method   isset_recursive
 *   @param    array            $array
 *   @param    string            $keys
 *   @return   bool
 */
function isset_recursive(?array $array, string ...$keys) : bool
{
   if (!($array) OR !($keys))
   {
      return false;
   }

   $key = array_shift($keys);
   if (!array_key_exists($key,$array))
   {
      return false;
   }

   if (!$keys)
   {
      return true;
   }

   if (!is_array($array[$key]))
   {
      return false;
   }

   $call[] = $array[$key];
   $call = array_merge($call,$keys);
   return call_user_func_array("isset_recursive",$call);
}


/**
 *   test if an element in an array exists and
 *   returns its value if it does not return $unset
 *
 *   @method   ifset
 *   @param    array   $array
 *   @param    string   $key
 *   @param    mixed   $unexist
 *   @return   mixed
 */
function ifset(?array $array, string $key, $unset = NULL)
{
   if (!$array)
   {
      return $unset;
   }
   return (array_key_exists($key,$array)?$array[$key]:$unset);
}

/**
 *   test if an element in element, recursive an array
 *   exists and returns its value if it does not return $unset
 *
 *   @method   ifset_recursive
 *   @param    array            $array
 *   @param    mixed            $unset
 *   @param    string           $keys...
 *   @return   mixed
 */
function ifset_recursive(?array $array, $unset = NULL, string ...$keys)
{
   if ((!$array) OR (!$keys))
   {
      return $unset;
   }

   $key = array_shift($keys);
   if (!array_key_exists($key,$array))
   {
      return $unset;
   }

   if (!$keys)
   {
      return $array[$key];
   }

   if (!is_array($array[$key]))
   {
      return $unset;
   }

   $call[] = $array[$key];
   $call[] = $unset;
   $call = array_merge($call,$keys);
   return call_user_func_array("ifset_recursive",$call);
}

/**
 *   test if is numeric return value if is not return $unnull
 *   @method   ifnumeric
 *   @param    mixed      $num
 *   @param    mixed      $unnull
 *   @return   mixed
 */
function ifnumeric($num, $unnull = NULL)
{
   return (is_numeric($num)?$num:$unnull);
}

/**
 *   calculates the engineering notation,
 *   and inserts the related prefix.
 *
 *   @method   numberPrefix
 *   @param    int            $value
 *   @param    boolean        $bit      is 1024
 *   @param    array          $prefix
 *   @return   string                   number is formatted
 */
function numberPrefix(int $value, bool $bit = false,
   array $prefix = array('', 'k', 'M', 'G', 'T', 'P')) : string
{
   $bit = $bit?1024:1000;
   $pow = floor(($value?log($value):0) / log($bit));
   $pow = min($pow, count($prefix)-1);
   $value /= pow($bit, $pow);
   return  round($value,3) . ' ' . $prefix[$pow];
}

/**
 *   List all files and sub directory files.
 *
 *   @method   listFiles
 *   @param    string      $path
 *   @param    string      $mask
 *   @param    boolean     $subPath
 *   @return   array
 */
function listFiles(string $path, string $mask, bool $subPath = true) : array
{
   $result = array();
   foreach (glob($path.'/'.$mask) as $filePath)
   {
      if ((substr(basename($filePath),0,1) <> '_')
      AND (!is_dir ($filePath)))
      {
         $result[] = $filePath;
      }
   }

   if ($subPath)
   foreach (glob($path.'/*', GLOB_ONLYDIR) as $directory)
   {
      if (substr(basename($directory),0,1) <> '_')
      {
         $result = array_merge($result,listFiles($directory, $mask));
      }
   }

   return $result;
}

function DateToStr(?string $value, string $format = 'H:i:s')
{
   if (!$value)
      return '';
   if (is_numeric($value))
      $value=gmdate('Y-m-d H:i:s', $value);
   return (new \DateTime($value))->format($format);
}


?>
