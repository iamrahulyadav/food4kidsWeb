<?php

namespace App\Http\Controllers\Api\Auth;
use Exception;
use Laravel\Passport\Client;

use App\Event;
use App\WeeklyEvent;

use App\Participate;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ParticipateController extends Controller
{
    use IssueTokenTrait;
    private $client;

    public function __construct(){
        $this->client = Client::find(1);
       }
   



     public function showParticipation()
     {
          $participation = DB::table('participate_event')
                ->orderBy('participate_id', 'desc')
                ->get();
       
         return response()->json(['data' => $participation], 200, [], JSON_NUMERIC_CHECK);
     }
   // user participate function
    public function requestToParticipate(Request $request)
    {
        $this->validate($request, [ 
            'user_id'=>'required',
            'event_id' => 'required',
            'user_startTime' => 'required',
            'user_endTime' => 'required',
            'admin_approveStatus'=>'required'
                  ]);



    $event_exists = Participate::where(['user_id'=>Auth::user()->id,'event_id'=>request('event_id')])->get();
            if(count($event_exists)  >0) {
                return response()->json(['data' => "User can not add multiple entry for single Task"], 200, [], JSON_NUMERIC_CHECK);
                }
                else

        $event = Participate::create([
            'user_id'=>Auth::user()->id,
            'event_id' => request('event_id'),
            'user_startTime' => request('user_startTime'),
            'user_endTime' => request('user_endTime'),
            'admin_approveStatus'=>request('admin_approveStatus')                  
        ]);
       
     return response()->json(['data' => $event], 200, [], JSON_NUMERIC_CHECK);
      }

    
     //for updating participation Information
      public function Participateupdate(Request $request)
       {
        try{
        $user_id = \Auth::user()->id;
       $participate_id = request('participate_id'); 
      $this->validate($request, [ 
            'user_id'=>'required',
            'event_id' => 'required',
            'user_startTime' => 'required',
            'user_endTime' => 'required',
            'admin_approveStatus'=>'required'
                  ]);

          $participate= Participate::where('participate_id',$participate_id)->update([
             'user_id'=>request('user_id'),
            'event_id' => request('event_id'),
            'user_startTime' => request('user_startTime'),
            'user_endTime' => request('user_endTime'),
            'admin_approveStatus'=>request('admin_approveStatus')     
             ]);
   return response()->json(['status' =>'success'], 200, [], JSON_NUMERIC_CHECK); 
      } 
      catch (Exception $e) {
       return response()->json(['status'=>'failed'],203);   
      }
    }



     //function to delete event
    public function destroy(Request $request)
    {
        //todo check if user exists linked to this
      try{
        $participation = Participate::find(request('participate_id'));
        $participation->delete();
        return response()->json(['status'=>'success'],200,['data'=>'Participation Removed Successfully']);
      }

      catch (\Exception $e) {
            return response()->json(['status'=>'failed'],203);
          }        
    }

  //function for sorting schedule
      public function ViewUserWithEventTask(Request $request)
      {

             //   $weeksort=WeeklyEvent::where('weekly_eventTask','Sorting')->get();
       $week=DB::table('weeklyevents')->join('participate_event','weeklyevents.event_id','=','participate_event.event_id')
          ->join('users','users.id','=','participate_event.user_id')
          ->where(['date'=>request('date')])
       ->select('users.id',
        'users.name',
        'users.email',
        'users.address',
        'users.city',
        'users.postal_code',
        'users.mobile',
        'weeklyevents.date',
        'weeklyevents.w_event_id',
        'participate_event.event_id',
        'participate_event.participate_id',
        'participate_event.admin_approveStatus',
        'participate_event.user_startTime',
        'participate_event.user_endTime')->get();


             /*  $weeksort= DB::table('weeklyevents')->where('weekly_eventTask', 'Sorting')->pluck('date'); */
       
         return response()->json(['data' => $week], 200, [], JSON_NUMERIC_CHECK);
      }

        public function viewUserPartVolunteer(Request $request){
        $user = request('id');                       
        $result= DB::table('weeklyevents')->join('participate_event',
                         'weeklyevents.event_id', '=', 'participate_event.event_id')
                         ->where('participate_event.user_id',$user)
                        ->get();                     
       return response()->json(['data' => $result], 200, [], JSON_NUMERIC_CHECK);
      }

    
  
}
