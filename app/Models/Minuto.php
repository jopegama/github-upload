<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minuto extends Model
{

    protected $table = 'Minuto';
    protected $primaryKey =  'MinAbsUTC';
	public $incrementing = false;
    public $timestamps = false;
//    protected $fillable = array('MinAbsUTC');
    //protected $fillable = array('PROCESS_INVENTARY', 'PROCESS_ACQUISITION_VALUE', 'CATEGORY_ID', 'DISTRICT_ID', 'PROCESS_ADDRESS', 'PROPERTYTYPE_ID', 'PROCESS_PROPERTY_AREA');
    //protected $visible = array( 'PROCESS_ID', 'PROCESS_INVENTARY', 'PROCESS_ACQUISITION_VALUE', 'CATEGORY_ID', 'DISTRICT_ID', 'PROCESS_ADDRESS', 'PROPERTYTYPE_ID', 'PROCESS_PROPERTY_AREA');
}