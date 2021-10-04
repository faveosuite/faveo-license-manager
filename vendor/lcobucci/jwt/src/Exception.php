<?php
<<<<<<< HEAD
<<<<<<< HEAD

namespace Lcobucci\JWT;

if (PHP_MAJOR_VERSION === 5) {
    interface Exception
    {
    }
} else {
    interface Exception extends \Throwable
    {
    }
=======
declare(strict_types=1);

namespace Lcobucci\JWT;

use Throwable;

interface Exception extends Throwable
{
>>>>>>> 22c0e54 (table changes)
=======

namespace Lcobucci\JWT;

if (PHP_MAJOR_VERSION === 5) {
    interface Exception
    {
    }
} else {
    interface Exception extends \Throwable
    {
    }
>>>>>>> f330c64 (optimization in progress)
}
