<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\Models\User;
use App\Models\Categories;
use App\Models\Products;
use App\Models\RecentlyViewedProducts;

use Auth;

class AccountController extends Controller
{
    public function __construct(Request $request)
    {

    }

    public function Signup(Request $request)
    {
        try
        {   
            /* 
                * validatiors to check the API request parameters 
                * using unique check in email to check whether the requested email is 
                  already in database or not
            */

        	$validator = \Validator::make($request->all(), [
                                                            	'name' => 'required|min:4', 
            													'email' => 'required|email|unique:users', 
            													'password' => 'required|min:6', 
            													'confirm_password' => 'required|same:password',  
                                                            ]);

            if ($validator->fails()) 
            {   
                return response()->json(array("msg"=>$validator->errors(),'status'=>"0"), 401);
            }

            $input = $request->all();

            $data = array(
            				"name"	=> $input['name'],
            				"email"	=> $input['email'],
            				"password" => Hash::make($input['password']),
            );

            if ($user = User::create($data)) 
            {   
                //generating authorize token in order to access restricted routes
            	$result['token'] = $user->createToken('Ropstam')->accessToken; 
            	$result['name'] = $input['name'];

            	return response()->json(array("status"=>"1", "msg"=>"Account created Successfully","result"=>$result), 200);
            }
            else
            {
                return response()->json(array("msg"=>"Something went wrong. Try Again Later",'status'=>"0"), 401);
            }
        }
        catch (Exception $e)
        {
            return response()->json($e,500);
        }
    }

    public function Login(Request $request)
    {
        try 
        {
        	$validator = \Validator::make($request->all(), [
            													'email' => 'required|email', 
            													'password' => 'required', 
                                                            ]);

            if ($validator->fails()) 
            {   
                return response()->json(array("msg"=>$validator->errors(),'status'=>"0"), 401);
            }

            $input = $request->all();

            if(Auth::attempt(['email' => $input['email'], 'password' =>$input['password']]))
            { 
	            $user = Auth::user(); 
	            
                //generating authorize token in order to access restricted routes
	            $result['token'] =  $user->createToken('Ropstam')->accessToken; 
	            
	            return response()->json(array("status"=>"1", "msg"=>"Login Successfully","result"=>$result), 200);
            
        	} 
        	else
        	{ 
            	return response()->json(['status'=>'0','msg'=>"Invalid Credentials"], 401); 
        	} 

        } 
        catch (Exception $e) 
        {
            return response()->json($e,500);
        }
    }

    public function HomeData(Request $request)
    {
        try 
        {       

           $categories = Categories::select(['id','name','image'])->where('is_deleted',0)->limit(6)->get();


           //getting from products table based on orderBy selling count to get most selling product on top
           $trending_products       = Products::select(['id','name','image','rent'])->where('is_deleted',0)->orderBy('selling_count','desc')->limit(20)->get();

           /*
                * recent view products are those which are recently viewed by user by hitting
                  product-details API 
           */
           $recently_viewed = RecentlyViewedProducts::where('user_id',Auth::user()->id)->orderBy('updated_at','desc')->limit(20)->get();

           
           $recent_view_products = [];
           foreach ($recently_viewed as $key) 
           {
                $recent_view_products[]    = Products::select(['id','name','image','rent'])->where('is_deleted',0)->where('id',$key['product_id'])->first();
           }

           $result = array(
                    "categories"    => $categories,
                    "trending_products" => $trending_products,
                    "recent_view_products"  => $recent_view_products,
           );

            return response()->json(['status'=>'1','msg'=>"Home Data","result"=>$result], 200); 


        } 
        catch (Exception $e) 
        {
            return response()->json($e,500);
        }
    }


    public function ProductDetails(Request $request)
    {
        try 
        {       

            $validator = \Validator::make($request->all(), [
                                                                'product_id' => 'required', 
                                                            ]);

            if ($validator->fails()) 
            {   
                return response()->json(array("msg"=>$validator->errors(),'status'=>"0"), 401);
            }

            $input = $request->all();

            $product = Products::select(['id','name','image','rent','sizes','rating'])->where('id',$input['product_id'])->where('is_deleted',0)->first();

            if ($product == "") 
            {
                return response()->json(['status'=>'0','msg'=>"Product not found"], 401); 
            }

            /* 
                * Right now I'm using default values for rating_count and reviews_count for all 
                  product as a dummy data. 
                * for fully functional we can get this value from a separate table i.e. 
                  ProductReviews
            */
            $product['rating_count'] = 1034;
            $product['reviews_count'] = 104;



            //insert into recent view product table
            if (RecentlyViewedProducts::where(['user_id' => Auth::user()->id, 'product_id'=>$input['product_id']])->count() == 0) 
            {
                // insert
                RecentlyViewedProducts::insert(array(
                    "user_id"       => Auth::user()->id,
                    "product_id"    => $input['product_id'],
                    "created_at"    => date("Y-m-d H:i:s"),
                    "updated_at"    => date("Y-m-d H:i:s"),
                ));
            }
            else
            {
                //update
                RecentlyViewedProducts::where(['user_id'=>Auth::user()->id, 'product_id'=>$input['product_id']])->update(array(
                    "updated_at"    => date("Y-m-d H:i:s"),
                ));
            }


            return response()->json(['status'=>'1','msg'=>"Product Details","result"=>$product], 200); 


        } 
        catch (Exception $e) 
        {
            return response()->json($e,500);
        }
    }
}
