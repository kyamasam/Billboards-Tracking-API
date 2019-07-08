<?php

namespace App\Http\Controllers;

use App\CampaignStatus;
use App\Http\Resources\CampaignStatusCollection;
use App\Http\Resources\CampaignStatusResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;

class CampaignStatusController extends Controller
{
    use BaseTraits;
    /**
     * @return CampaignStatusCollection
     *
     */
    public function index()
    {
        //anyone can access this
        return new CampaignStatusCollection(CampaignStatus::paginate());

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
            "name" => "required",
            "description" => "required",
        ]);



        $input = $request->all();





        $campaignStatus = new CampaignStatus();
        $campaignStatus->name = $input['name'];
        $campaignStatus->description = $input['description'];


        $campaignStatus->save();
        return response (new CampaignStatusResource($campaignStatus))->setStatusCode(200);


    }

    /**
     * @param $id
     * @return CampaignStatusResource
     *
     */

    public function show($id)
    {
        $campaignStatus = CampaignStatus::find($id);
        return new CampaignStatusResource($campaignStatus);
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
            "name" => "required",
            "description" => "required",
        ]);

        $input = $request->all();


        $schedule = CampaignStatus::find($id);

        $schedule->name = $input['name'];
        $schedule->description = $input['description'];

        $schedule->save();
        return response (new CampaignStatusResource($schedule))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        CampaignStatus::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }

}
