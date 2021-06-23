<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;


    /*
    	* I used getRentAttribute in order to return rent in specific format where ever it is called 
    	  in the whole application
    	* same for sizes (as sizes are store in serialize format in database because it can be
    	  multiple)
	*/
    public function getRentAttribute($rent) {
       return "₹".$rent."/month";
    }

    public function getSizesAttribute($sizes) {
       return unserialize($sizes);
    }
}
