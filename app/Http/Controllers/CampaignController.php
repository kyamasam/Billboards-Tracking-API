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
use App\Schedule;
use App\User;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
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
        $owner = User::find($input['owner_id']);
        if (!isset($owner)){
            return $this->ErrorReporter('User Not Found', 'User Id passed was not found in the database',422);
        }
        $campaign->owner_id= $input['owner_id'];

        // if the budget_id  is passed, confirm that the budget actually exists
        if(isset($input['budget_id'])){
            $budget = Budget::find($input['budget_id']);
            return $this->ResourceNotFound($budget,'Budget');
        }
        $campaign->budget_id= $input['budget_id'];

        //if the budget_id is passed check that budget exists
        if(isset($input['budget_id'])){
            $schedule = Schedule::find($input['schedule_id']);
            return $this->ResourceNotFound($schedule,'Schedule');
        }
        $campaign->schedule_id= $input['schedule_id'];

        //if the campaign_status is passed check that campaign_status exists in the DB

        if(isset($input['campaign_status'])){
            $campaign_status = CampaignStatus::find($input['campaign_status']);
            return $this->ResourceNotFound($campaign_status,'Campaign Status');
        }
        $campaign->campaign_status= $input['campaign_status'];


        $campaign->save();
        return response (new CampaignResource($campaign))->setStatusCode(200);


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
        $campaign->owner_id= $input['owner_id'];
        $campaign->budget_id= $input['budget_id'];
        $campaign->schedule_id= $input['schedule_id'];
        $campaign->campaign_status= $input['campaign_status'];


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

//        return response()->json($selections);

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

}
