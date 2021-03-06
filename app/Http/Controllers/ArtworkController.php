<?php

namespace App\Http\Controllers;

use App\Artwork;
use App\Campaign;
use App\Http\Resources\ArtworkCollection;
use App\Http\Resources\ArtworkResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            "image_src" => "required|file",
            "campaign_id" => "required|numeric",
            "billboard_id" => "required|numeric",
            "file_type" => "required",
            "animate" => "required",
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

        $now = strtotime(date("h:i:sa"));

        $artwork = new Artwork();
        $artwork->height= $input['height'];
        $artwork->width= $input['width'];
        $artwork->campaign_id= $input['campaign_id'];
        $artwork->billboard_id= $input['billboard_id'];
        $artwork_image_ext=$request->file('image_src')->getClientOriginalExtension();
        $artwork_image_file = $request->file('image_src');
        $artwork_image_file_name= 'art'.$request->campaign_id.$now.'.'.$artwork_image_ext;
        Storage::disk('local')->putFileAs('public/artwork',$artwork_image_file,$artwork_image_file_name);
        $artwork->image_src= env('MEDIA_SERVER_URL').'artwork/'.$artwork_image_file_name;


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
        $artwork = Artwork::with(['Campaigns','Billboards'])->get()->keyBy('id');

        return new ArtworkResource($artwork[$id]);
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
        $artwork->update($input);
        $artwork->save();
        return response (new ArtworkResource($artwork))->setStatusCode(200);
    }

    public function update_image(Request $request, $id){
        $now = strtotime(date("h:i:sa"));
        $this->validate($request, [
            "image_src" => "required|file",
        ]);

        $artwork = Artwork::find($id);
        //get the original file
        $original_image_path = $artwork->image_src;

        //clean up path of old artwork
        $original_image_path = str_replace(env('MEDIA_SERVER_URL'),"",$original_image_path);
        //delete existing artwork
        File::delete(env('MEDIA_SERVER_FOLDER').$original_image_path);

        $artwork_image_ext=$request->file('image_src')->getClientOriginalExtension();
        $artwork_image_file = $request->file('image_src');
        $artwork_image_file_name= 'art'.$request->campaign_id.$now.'.'.$artwork_image_ext;
        Storage::disk('local')->putFileAs('public/artwork',$artwork_image_file,$artwork_image_file_name);
        $artwork->image_src= env('MEDIA_SERVER_URL').'artwork/'.$artwork_image_file_name;
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
