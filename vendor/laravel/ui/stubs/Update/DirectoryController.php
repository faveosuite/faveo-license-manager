<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\PDF;
class DirectoryController extends Controller
{
    public function setDirectory(Request $request){

        
        $archives = $request->get('archives_path');
        $queries =  $request->get('query_path');
        DB::table('directory')->insertOrIgnore([
            'ARCHIVES_DIRECTORY'=> $archives,
            'QUERIES_DIRECTORY'=>$queries
        ]);

        return response(['message'=>'The path is set']);
    }
    
}
