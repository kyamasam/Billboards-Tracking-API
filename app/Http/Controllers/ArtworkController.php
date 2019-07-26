<?php

namespace App\Http\Controllers;

use App\Artwork;
use App\Campaign;
use App\Http\Resources\ArtworkCollection;
use App\Http\Resources\ArtworkResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
class ArtworkController extends Controller
{
   use BaseTraits;

    /**
     * @return ArtworkCollection
     *
     */
    public function index()
    {
        //anyone can access this
        return new ArtworkCollection(Artwork::paginate());

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
            "height" => "required|numeric",
            "width" => "required|numeric",
            "image_src" => "required|string",
            "campaign_id" => "required|numeric",
            "billboard_id" => "required|numeric",
        ]);


        $input = $request->all();

        //check if the passed campaign_id is valid
        $campaign_available = $this->ValidateAvailability(app("App\Campaign"),$input['campaign_id'], 'Campaign');

        if (!($campaign_available == "true")){
            return $campaign_available;
        }
        //check if the passed billboard_id is valid
        $billboard_available = $this->ValidateAvailability(app("App\Billboard"),$input['billboard_id'], 'Billboard');

        if (!($billboard_available == "true")){
            return $billboard_available;
        }




        $artwork = new Artwork();
        $artwork->height= $input['height'];
        $artwork->width= $input['width'];
        $artwork->campaign_id= $input['campaign_id'];
        $artwork->billboard_id= $input['billboard_id'];
        $artwork->image_src= $input['image_src'];


        $artwork->save();
        return response (new ArtworkResource($artwork))->setStatusCode(201);


    }

    /**
     * @param $id
     * @return ArtworkResource
     *
     */

    public function show($id)
    {
        $artwork = Artwork::find($id)->with(['Campaigns','Billboards'])->first();

        return new ArtworkResource($artwork);
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
            "height" => "required|numeric",
            "width" => "required|numeric",
            "image_src" => "required|string",
            "campaign_id" => "required|numeric",
            "billboard_id" => "required|numeric",
        ]);


        $input = $request->all();

        //check if the passed campaign_id is valid
        $campaign_available = $this->ValidateAvailability(app("App\Campaign"),$input['campaign_id'], 'Campaign');

        if (!($campaign_available == "true")){
            return $campaign_available;
        }
        //check if the passed billboard_id is valid
        $billboard_available = $this->ValidateAvailability(app("App\Billboard"),$input['billboard_id'], 'Billboard');

        if (!($billboard_available == "true")){
            return $billboard_available;
        }




        $artwork = Artwork::find($id);
        $artwork->height= $input['height'];
        $artwork->width= $input['width'];
        $artwork->campaign_id= $input['campaign_id'];
        $artwork->billboard_id= $input['billboard_id'];
        $artwork->image_src= $input['image_src'];


        $artwork->save();
        return response (new ArtworkResource($artwork))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        Artwork::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }
}
