<?php

namespace App\Exception;

use Exception;

class RouterException extends Exception
{
      public function __consttuct($message = "", $code = 1)
      {
            parent::__construct($message, $code);
      }
}
