<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use Carbon\Carbon;
use App\Models\AflClients;
use Illuminate\Support\Facades\Lang;




/**
 * Consist of functionalities for the client page in Auto Faveo licenser 
 * Class ClientsController
 * @package App\Http\Controllers\Admin
 */
class ClientsController extends Controller
{

    /**
     * Stores newly added clients into the database
     * @param ClientRequest $request
     *
     * @return  response that a new client is added
    */
    public function store(ClientRequest $request){

        $date = Carbon::now();
        $client = new AflClients(array(
            'client_fname'=> $request->get('client_fname'),
            'client_lname'=>$request->get('client_lname'),
            'client_email'=> $request->get('client_email'),
            'client_status'=> $request->get('client_status'),
           
            
        ));
        // change the active and cancel date based on the client_status
        if($request->get('client_status')){
            $client->client_active_date = $date->toDateString();
         }
        else{
            
            $client->client_cancel_date = $date->toDateString();
        }

        $client->save();
        return \successResponse(Lang::get('lang.Client_ADD'),$clients,201);

    }

  

    /**
     * shows newly added clients from the database
     * 
     *
     * @return response that a client is deleted
    */
    public function show(){

        $clients  = AflClients::all();
        return \successResponse(Lang::get('lang.Client_Show'),$clients,200);
    }




     /**
     * Deletes the clients from the database based on the id
     * @param $client_id
     *
     * @return response that a client is deleted
    */
     public function destroy($client_id)
     {
        $client = AflClients::where('client_id',$client_id)->firstOrFail();
        $client->delete();
        return \successResponse(Lang::get('lang.Client_Destroy'),$client,200);
     }



        /* public function edit($client_id)
       {

        $client = afl_clients::where('client_id',$client_id)->firstOrFail();
        return view('',compact('client'));

       }*/



    /**
     * Updates the clients from the database based on the id
     * 
     * @param Request $request
     * @param $client_id
     *
     * @return response that a client details is edited
    */
    public function update(Request $request, $client_id)
    {
        $client = AflClients::where('client_id',$client_id)->firstOrFail();
        $client->client_fname = $request->get('client_fname');
        $client->client_lname = $request->get('client_lname');
        $client->client_email = $request->get('client_email');
        $client->client_status = $request->get('client_status');
        // Edit the active and cancel date according to the client_status
        if($request->get('client_status')){
            $client->client_active_date = $date->toDateString();
            }
        else{
            
            $client->client_cancel_date = $date->toDateString();
        }

        $client->save();
        return \successResponse(Lang::get('lang.Client_Update'),$client,201);

       
    }

}
