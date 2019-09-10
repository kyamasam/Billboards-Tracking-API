<?php

namespace App\Http\Controllers;

use App\Artwork;
use App\Billboard;
use App\BillboardCampaign;
use App\Budget;
use App\Campaign;
use App\CampaignStatus;
use App\Http\Resources\BillboardCollection;
use App\Http\Resources\CampaignCollection;
use App\Http\Resources\CampaignResource;
use App\Jobs\SendEmailJob;
use App\Mail\CampaignStatusChanged;
use App\Schedule;
use App\ScheduleTimes;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Node\Expr\Cast\Object_;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CampaignController extends Controller
{
     use BaseTraits;

    /**
     * @return CampaignCollection
     *
     */
    public function index()
    {
        //anyone can access this
        return new CampaignCollection(Campaign::paginate());

    }


    /**
     * @param Request $request
     * @return CampaignResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     *
     */

    public function store(Request $request)
    {


        $this->validate($request, [
            "campaign_name" => "required|string",
            "campaign_description" => "required|string",
            "owner_id" => "required|numeric",
        ]);


        $input = $request->all();


        $campaign = new Campaign();
        $campaign->campaign_name= $input['campaign_name'];

        //check that the owner exists
        if($this->ValidateAvailabilityModel(app("App\User"),$input['owner_id'])){
            $campaign->owner_id= $input['owner_id'];
        }
        else{
            return $this->ErrorReporter('User Not found','the User id passed was not found',422);
        }


        // if the budget_id  is passed, confirm that the budget actually exists
        if(isset($input['budget_id'])){
            //check that the budget exists
            if($this->ValidateAvailabilityModel(app("App\Budget"),$input['budget_id'])){
                $campaign->budget_id= $input['budget_id'];
            }
            else{
                return $this->ErrorReporter('Budget Not found','the Budget id passed was not found',422);
            }
        }

        //if the budget_id is passed check that budget exists
        if(isset($input['schedule_id'])){
            //check that the budget exists
            if($this->ValidateAvailabilityModel(app("App\Schedule"),$input['schedule_id'])){
                //pass
                $campaign->schedule_id= $input['schedule_id'];
            }
            else{
                return $this->ErrorReporter('Schedule Not found','the Schedule id passed was not found',422);
            }
        }


        $campaign->save();
        return new CampaignResource(Campaign::find($campaign->id));

    }

    /**
     * @param $id
     * @return CampaignResource
     *
     */

    public function show($id)
    {
        $campaign = Campaign::with(['Owner','Budget','Billboards','CampaignStatus', 'Schedule' =>function($query) use ($id){
            $query->where('id',3);
        }])->get()->keyBy('id');
        return new CampaignResource($campaign[$id]);
    }



    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            "campaign_name" => "required|string",
            "owner_id" => "required|numeric",
        ]);

        $input = $request->all();


        $campaign = Campaign::find($id);
        $campaign->campaign_name= $input['campaign_name'];

        //check that the owner exists
        if($this->ValidateAvailabilityModel(app("App\User"),$input['owner_id'])){
            $campaign->owner_id= $input['owner_id'];
        }
        else{
            return $this->ErrorReporter('User Not found','the User id passed was not found',422);
        }


        // if the budget_id  is passed, confirm that the budget actually exists
        if(isset($input['budget_id'])){
            //check that the budget exists
            if($this->ValidateAvailabilityModel(app("App\Budget"),$input['budget_id'])){
                $campaign->budget_id= $input['budget_id'];
            }
            else{
                return $this->ErrorReporter('Budget Not found','the Budget id passed was not found',422);
            }
        }

        //if the budget_id is passed check that budget exists
        if(isset($input['schedule_id'])){
            //check that the budget exists
            if($this->ValidateAvailabilityModel(app("App\Schedule"),$input['schedule_id'])){
                //pass
                $campaign->schedule_id= $input['schedule_id'];
            }
            else{
                return $this->ErrorReporter('Schedule Not found','the Schedule id passed was not found',422);
            }
        }
        $campaign->save();
        return response (new CampaignResource($campaign))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        Campaign::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }


    /**
     * @param null $campaign_id
     * @return BillboardCollection|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */

    public function SelectedLocations($campaign_id)
    {
        $selections= BillboardCampaign::where('campaign_id','=',$campaign_id)->pluck('billboard_id');
        $billboards = Billboard::all()->whereIn('id',$selections);
        return new BillboardCollection($billboards);
    }



    public function Locations(Request $request){

        // todo prevent duplicates
        // todo find a more efficient method for saving resoces instead of loop
        $this->validate($request,[
            "campaign_id" => "required|numeric",
            "billboards" => "required|string",
        ]);
        $input = $request->all();
        $campaign = $input['campaign_id'];
        $billboards = explode(',',$input['billboards']);

        foreach($billboards as $billboard){
           $new_billboard= new BillboardCampaign();
            $new_billboard->billboard_id = $billboard;
            $new_billboard->campaign_id = $campaign;
            $new_billboard->save();
        }

        $selected_billboards = Billboard::all()->whereIn('id', $billboards);
        return new BillboardCollection($selected_billboards);

    }


    public function removeSelections(Request $request){
        $input= $request->all();

        $billboards = explode(',',$input['billboards']);
        //
        $prev_selections= BillboardCampaign::where('campaign_id','=',$input['campaign_id'])->whereIn('billboard_id',$billboards)->pluck('id');

        BillboardCampaign::destroy([$prev_selections]);
        return $this->SuccessReporter('Records Deleted', 'Record was successfully deleted',200);
    }

    public function campaignsFiltered(int $status=1){
        $campaign_select = Campaign::where('campaign_status','=',$status)->paginate();

        return new CampaignCollection($campaign_select);
    }

    public function bulk_create(Request $request){
        $input = $request->all();


        //create time slots
        //no need for validation
        $schedule_times=$input["time_slot"];
        $schedule = new Schedule();
        $schedule->save();
        $schedule_id=$schedule->id;
        foreach ($schedule_times as &$schedule_time){
            $schedule_time['schedule_id']=$schedule_id;
        }
        try{
            ScheduleTimes::insert($schedule_times);
        }catch (QueryException $exception){
            return $this->ErrorReporter("Schedule Data is invalid", "The passed Schedule data has an incorrect format",422);
        }
        //end create time slots

        $campaign_array = (array) $input["campaign_details"];
        //create campaign
        $campaign_rules= [
            "campaign_name" => "required|string",
            "campaign_description" => "required|string",
            "owner_id" => "required|numeric",
        ];
        $validator = Validator::make($campaign_array, $campaign_rules);

        if ($validator->passes()) {

        } else {
            //Handle the errors
            return response()->json(["message"=>"The given data was invalid", "errors"=>$validator->errors()]);
        }

        $campaign_object=$input["campaign_details"];


        $campaign = new Campaign();
        $campaign->campaign_name= $campaign_object['campaign_name'];
        $campaign->campaign_description= $campaign_object['campaign_description'];
        //check that the owner exists
        if($this->ValidateAvailabilityModel(app("App\User"),$campaign_object['owner_id'])){
            $campaign->owner_id= $campaign_object['owner_id'];
        }
        else{
            return $this->ErrorReporter('User Not found','the User id passed was not found',422);
        }
        //save campaign
        $campaign->save();


        //create budget
        $budget_object=$input["budget"];
        $budget_rules= [
            "total_animation_cost" => "required|numeric",
            "total_campaign_cost" => "required|numeric",
            "final_cost" => "required|numeric",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
        ];
        $validator = Validator::make($budget_object, $budget_rules);
        if ($validator->passes()) {

            $start_date = new \DateTime($budget_object['start_date']);
            $end_date = new \DateTime($budget_object['end_date']);
            $today = new \DateTime(date('Y-m-d'));

            //check that the end date is not less than start date
            //convert to DateTime
            if(!($start_date <= $end_date)){
                return $this->ErrorReporter('Invalid Data','The End Date Must be equal to or greater than the start date', 422);

            }elseif (!($today<=$start_date)){
                return $this->ErrorReporter('Invalid Data','The Start Date Must be Later than or equal to today', 422);
            }

            $budget = new Budget();
            $budget->start_date= $budget_object['start_date'];
            $budget->end_date= $budget_object['end_date'];
            $budget->total_animation_cost = $budget_object['total_animation_cost'];
            $budget->total_campaign_cost = $budget_object['total_campaign_cost'];
            $budget->final_cost = $budget_object['final_cost'];

            $budget->save();
        } else {
            //Handle error
            return response()->json(["message"=>"The given data was invalid", "errors"=>$validator->errors()]);
        }

        //select locations
        $location_object=$input["locations"];
        $location_rules=[
            "billboards" => "required|string",
        ];
        $validator = Validator::make($location_object, $location_rules);
        if ($validator->passes()) {
            $billboards = explode(',',$location_object['billboards']);
            foreach($billboards as $billboard){
                $new_billboard= new BillboardCampaign();
                $new_billboard->billboard_id = $billboard;
                $new_billboard->campaign_id = $campaign->id;
                $new_billboard->save();
            }
        } else {

            //Handle error

            return response()->json(["message"=>"The given data was invalid", "errors"=>$validator->errors()]);

        }

        //create artwork

        //convert object to associative array
        $artworks=$input["artwork"];
        $now = strtotime(date("h:i:sa"));


        foreach ($artworks as &$artwork){
            $artwork_object=$artwork;
            $artwork_array= (array)$artwork_object ;
            $rules=[
                "height" => "required|numeric",
                "width" => "required|numeric",
                "billboard_id" => "required|numeric",
                "image_src" => "required|file",
                "file_type"=>"required",
                "animate"=>"required",
            ];
            $validator = Validator::make($artwork_array, $rules);
            if ($validator->passes()) {
                // Handle data
                //check if the passed campaign_id is valid
                $campaign_available = $this->ValidateAvailability(app("App\Campaign"),$campaign->id, 'Campaign');

                if (!($campaign_available == "true")){
                    return $campaign_available;
                }
                //check if the passed billboard_id is valid
                $billboard_available = $this->ValidateAvailability(app("App\Billboard"),$artwork_object['billboard_id'], 'Billboard');

                if (!($billboard_available == "true")){
                    return $billboard_available;
                }

                $artwork = new Artwork();
                $artwork->height= $artwork_object['height'];
                $artwork->width= $artwork_object['width'];
                $artwork->campaign_id= $campaign->id;
                $artwork->file_type= $artwork_object['file_type'];
                $artwork->animate= $artwork_object['animate'];
                $artwork->billboard_id= $artwork_object['billboard_id'];

                //image file upload
                $original_image_path = $artwork_object['image_src'];

                //clean up path of old artwork
                $original_image_path = str_replace(env('MEDIA_SERVER_URL'),"",$original_image_path);
                //delete existing artwork
                File::delete(env('MEDIA_SERVER_FOLDER').$original_image_path);

                $artwork_image_ext=$artwork_object['image_src']->getClientOriginalExtension();
                $artwork_image_file = $artwork_object['image_src'];
                $artwork_image_file_name= 'art'.$request->campaign_id.$now.'.'.$artwork_image_ext;
                Storage::disk('local')->putFileAs('public/artwork',$artwork_image_file,$artwork_image_file_name);
                $artwork->image_src= env('MEDIA_SERVER_URL').'artwork/'.$artwork_image_file_name;


                $artwork->save();
            } else {
                //Handle your error
                return response()->json(["message"=>"The given data was invalid", "errors"=>$validator->errors()]);
            }
        }


        //update campaign
        $campaign=Campaign::find($campaign->id);
        $campaign->budget_id=$budget->id;
        $campaign->schedule_id=$schedule->id;
        $campaign->save();
        $campaign_bulk = Campaign::with(['Owner','Budget','CampaignStatus','Artwork', 'Schedule'])->get()->keyBy('id');

        return new CampaignResource($campaign_bulk[$campaign->id]);

    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function updateCampaignStatus(Request $request, $id)
    {

        $this->validate($request, [
            "campaign_status" => "required|numeric",
        ]);
        //check if user owns this campaign or is agent /admin
        $owner_id=Campaign::find($id)->owner()->get()->pluck('id')->first();
        $current_user = auth()->user();
        if($owner_id == $current_user->id || $current_user->account_type == 2 || $current_user->account_type == 3){
            //do nothing
        }else{
            return $this->DefaultUnauthrized();
        }

        $input = $request->all();

        $campaign = Campaign::find($id);

        //if the campaign_status is passed check that campaign_status exists in the DB
        $passed_campaign_status = $input['campaign_status'];
        if(isset($input['campaign_status'])){
            if($this->ValidateAvailabilityModel(app("App\CampaignStatus"),$passed_campaign_status)){

                //charging the user if the status is set to active.
                if((int)$passed_campaign_status===2){
                    //check if the campaign is already active
                    if((int)$campaign->campaign_status === 2){
                        //the campaign is already activated. no need to activate it again
                        return $this->ErrorReporter('Already active','Campaign is aready active',422);
                    }


                    $campaign_user_wallet=$campaign->Owner()->first()->Wallet()->get();
                    //get schedules
                    $schedules = $campaign->Schedule()->first()->ScheduleTimes()->get();
                    //total cost of campaign
                    $campaign_cost = $campaign->Budget()->first()->final_cost;

                    $wallet_balance=$campaign_user_wallet[0]->credit_balance;
                    //compare the balance in the users account
                    if($wallet_balance>= $campaign_cost){
                        //the user has enough money
                        //deduct funds
                        $new_wallet_balance= $wallet_balance-$campaign_cost;
                        $campaign_user_wallet[0]->update(['credit_balance' => $new_wallet_balance]);
                        $campaign->update(array('campaign_status'=>$passed_campaign_status));
                    }else{
                        //this user is broke

                        return $this->ErrorReporter('Insufficient funds','CampaignStatus Could not be activated due to insufficient funds',422);
                    }

                }
                else
                {
                    $campaign->update(array('campaign_status'=>$passed_campaign_status));
                }

            }
            else{
                return $this->ErrorReporter('CampaignStatus Not found','the CampaignStatus id passed was not found',422);
            }
        }


        $campaign->save();

        $saved_campaign['campaign']=Campaign::find($id);
        $saved_campaign['comments']=Campaign::find($id)->pluck('admin_feedback')->all();
        $saved_campaign['user']=$request->user();
        $saved_campaign['status']=Campaign::find($id)->CampaignStatus()->first();
        //recipient
        //mailer_class
        $email_details['recipient']= $request->user();

        $email_details['mailer_class']= new CampaignStatusChanged($saved_campaign);
        SendEmailJob::dispatch($email_details);

        return response (new CampaignResource($campaign))->setStatusCode(200);
    }

    public function campaignsDaysFiltered(Request $request, $start_date, $end_date){
        //if the user has passed in days
        $input= $request->all();
        $days = $input['days'];
        $day_names = explode(',',$days);
        $campaigns = Campaign::with(['budget','Schedule.ScheduleTimes'])
            ->whereHas(
            'budget', function ($query) use($start_date,$end_date){
            $query->whereDate('start_date','<=',$start_date)->whereDate('end_date','>=',$end_date);
        })->whereHas('Schedule.ScheduleTimes', function ($query){
            $query->where('days','!=','');
            })
            ->get()->toArray();


        //create an array of campaign ids that satisfy the condition
        $selected_campaigns= array();

        //loop through the whole array
        foreach ($campaigns as $campaign){
            foreach ($campaign['schedule']['schedule_times'] as $schedule_time){
                $schedule_days= explode(',',$schedule_time['days']);
                //check if it contains the day_names
                foreach ($day_names as $day_name){
                   if(in_array($day_name,$schedule_days)){
                       array_push($selected_campaigns,$campaign['id']);
                   }

                }
            }

        }

        $structured_campaign=Campaign::whereIn('id',$selected_campaigns)->With(['Owner','Budget','CampaignStatus', 'Schedule','Schedule.ScheduleTimes'])->get();
        return new CampaignCollection($structured_campaign);

    }


    //filter by user id
    public function CampaignByUserId($id){
        $campaigns=User::find($id)->Campaign()->get();
        return new CampaignCollection($campaigns);
    }

}
