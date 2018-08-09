<?php

namespace App\Http\Controllers\Api;
use Auth;
use DB; 
use App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    //
   public function index(){

    	$userdata = Auth::user()->get();

    	$user = User::where('id',Auth::user()->id)->get();
     
    	return response()->json(['data' => $user], 200, [], JSON_NUMERIC_CHECK);
    }

    public function updateUser(Request $request){
  try {

       $user_id = \Auth::user()->id;
       $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'address'=>'required',
            'city'=>'required',
            'postal_code'=>'required',
            'mobile'=>'required'
                  ]);

          $user= User::where('id',$user_id)->update([
            'name' => request('name'),
            'email' => request('email'),
            'address' => request('address'),
            'city' => request('city'),
            'postal_code' => request('postal_code'),
            'mobile' => request('mobile')
                 ]);

       return response()->json(['status' =>'success'], 200, [], JSON_NUMERIC_CHECK);
        
      } catch (Exception $e) {      
     return response()->json(['status'=>'failed'],203);
              }
    }

  public function deleteUser(Request $request)
    {
      try{

        $user = User::find(request('id'));
        $user->delete();

        //return response()->json(['status'=>'success'],200);return redirect('/')->with('success', 'Event has been deleted!!');
  
        return response()->json(['status'=>'success'],200,['data'=>'Event Deleted Successfully']);
      }
      catch (\Exception $e) {
      return response()->json(['status'=>'failed'],203);      
      }  
   }
}
