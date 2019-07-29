<?php

namespace App\Http\Controllers;

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
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Null_;

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
        $campaign = Campaign::find($id)->with(['Owner','Budget','CampaignStatus', 'Schedule'])->first();

        return new CampaignResource($campaign);
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
        //todo: add admin check here
        //if the campaign_status is passed check that campaign_status exists in the DB

        if(isset($input['campaign_status'])){
            if($this->ValidateAvailabilityModel(app("App\CampaignStatus"),$input['campaign_status'])){
                $campaign->campaign_status= $input['campaign_status'];

            }
            else{
                return $this->ErrorReporter('CampaignStatus Not found','the CampaignStatus id passed was not found',422);
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

        $input = $request->all();

        $campaign = Campaign::find($id);

        //if the campaign_status is passed check that campaign_status exists in the DB

        if(isset($input['campaign_status'])){
            if($this->ValidateAvailabilityModel(app("App\CampaignStatus"),$input['campaign_status'])){
                $campaign->update(array('campaign_status'=>$input['campaign_status']));
            }
            else{
                return $this->ErrorReporter('CampaignStatus Not found','the CampaignStatus id passed was not found',422);
            }
        }



        $campaign->save();

        $saved_campaign['campaign']=Campaign::find($id);
        $saved_campaign['user']=$request->user();
        $saved_campaign['status']=Campaign::find($id)->CampaignStatus()->first();
        //recipient
        //mailer_class
        $email_details['recipient']= $request->user();

        $email_details['mailer_class']= new CampaignStatusChanged($saved_campaign);

//        dispatch(new SendEmailJob($email_details));
        SendEmailJob::dispatch($email_details);

        return response (new CampaignResource($campaign))->setStatusCode(200);
    }

}
