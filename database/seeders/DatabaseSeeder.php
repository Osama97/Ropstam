<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Categories;
use App\Models\Products;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {	
    	Categories::truncate();
    	Products::truncate();

    	Categories::insert([
            ['name' 		 =>'Bed Room'],
            ['name' 		 =>'Living Room'],
            ['name' 		 =>'DSLR Camera'],
            ['name' 		 =>'Appliances'],
            ['name' 		 =>'Storage'],
            ['name' 		 =>'Packages'],
        ]);

        Products::insert([
            ['name' =>'Sofa Baleria', 'rent'=>'799', 'refundable_deposit'=>'1899', 'rating'=>4.5, 'sizes'=>'a:4:{i:0;s:3:"6x3";i:1;s:3:"6x4";i:2;s:3:"6x5";i:3;s:3:"6x6";}', 'selling_count'=> 50],
            ['name' =>'Dining Table', 'rent'=>'949', 'refundable_deposit'=>'1200', 'rating'=>4.3, 'sizes'=>'a:5:{i:0;s:3:"2x1";i:1;s:3:"2x3";i:2;s:3:"2x4";i:3;s:3:"2x6";i:4;s:3:"2x8";}', 'selling_count'=> 45],
            ['name' =>'Fabric Sofa', 'rent'=>'999', 'refundable_deposit'=>'1500', 'rating'=>4.8, 'sizes'=>'a:3:{i:0;s:3:"5x3";i:1;s:3:"5x4";i:2;s:3:"5x5";}','selling_count'=> 30],
        ]);
    }
}
