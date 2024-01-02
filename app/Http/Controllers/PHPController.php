<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PHPController extends Controller
{public function execEnabled()
    {
        try {
            // make a small test
            return function_exists('exec') && ! in_array('exec', array_map('trim', explode(', ', ini_get('disable_functions'))));
        } catch (\Exception $ex) {
            return false;
        }
    }

    
}
