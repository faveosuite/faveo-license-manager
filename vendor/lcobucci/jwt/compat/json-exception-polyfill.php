<?php

<<<<<<< HEAD
if (PHP_VERSION_ID < 70300 && ! class_exists('JsonException')) {
=======
if (PHP_VERSION_ID < 70300) {
>>>>>>> f330c64 (optimization in progress)
    class JsonException extends Exception
    {
    }
}
