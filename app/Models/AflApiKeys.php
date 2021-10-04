<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AflApiKeys extends Model
{
    use HasFactory;
<<<<<<< HEAD
<<<<<<< HEAD
    protected $fillable = [ 
=======
    protected $fillable = [
>>>>>>> 22c0e54 (table changes)
           'api_key_secret',
           'api_key_ip',
           'api_key_clients_add',
           'api_key_clients_edit',
           'api_key_licenses_add',
           'api_key_licenses_edit' ,
           'api_key_products_add',
           'api_key_products_edit',
           'api_key_installations_edit',
<<<<<<< HEAD
           'api_key_search' ,
           'api_key_status'];
           
    protected $primaryKey = 'api_key_id';
    public $timestamps = false;

=======
>>>>>>> 34fd2bf (installlicense completed and correction of connection test done)
=======
           'api_key_versions_add',
           'api_key_verisons_edit',
           'api_key_search' ,
           'api_key_status'];

    protected $primaryKey = 'api_key_id';
    public $timestamps = false;

>>>>>>> 22c0e54 (table changes)
}
