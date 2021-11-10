<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Request\ClientRequest;
use Carbon\Carbon;
use App\Models\afl_clients;

class ClientsController extends Controller
{
    public function store(ClientRequest $request){
        $date = Carbon::now();
        $client = new afl_clients(arrray([
            'client_fname'=> $request->get('client_fname'),
            'client_lname'=>$request->get('client_lname'),
            'client_email'=> $request->get('client_email'),
             
            'client_status'=> $request->get('client_status'),
         
            
        ]));

    }

    public function show(){

        $clients  = afl_clients::all();
        return response($clients);
    }
}
