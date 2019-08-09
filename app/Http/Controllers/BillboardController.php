<?php

namespace App\Http\Controllers;

use App\Billboard;
use App\Http\Resources\BillboardCollection;
use App\Http\Resources\BillboardResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BillboardController extends Controller
{
    use BaseTraits;
    /**
     * @return BillboardCollection
     *
     */
    public function index()
    {
        //anyone can access this

        return new BillboardCollection(Billboard::paginate());

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "display_duration" => "required",
            "location_lat" => "required",
            "location_long" => "required",
            "placement" => "required",
            "billboard_picture" => "required|mimes:jpg,jpeg,png,bmp,tiff |max:4096",
            "average_daily_views" => "required",
            "definition" => "required",
            "dimensions_width" => "required",
            "dimensions_height" => "required",
            "description" => "required",
        ]);

        $input = $request->all();

        $now = strtotime(date("h:i:sa"));

        $billboard = new Billboard;
        $billboard->display_duration = $input['display_duration'];
        $billboard->location_lat = $input['location_lat'];
        $billboard->location_long = $input['location_long'];
        $billboard->placement = $input['placement'];
        $billboard_picture_ext=$request->file('billboard_picture')->getClientOriginalExtension();
        $billboard_picture_file = $request->file('billboard_picture');
        $billboard_picture_file_name= 'bb'.$request->location_lat.$now.'.'.$billboard_picture_ext;
        Storage::disk('local')->putFileAs('public/billboards',$billboard_picture_file,$billboard_picture_file_name);
        $billboard->billboard_picture = env('MEDIA_SERVER_URL').'billboards/'.$billboard_picture_file_name;
        $billboard->average_daily_views = $input['average_daily_views'];
        $billboard->definition = $input['definition'];
        $billboard->dimensions_width = $input['dimensions_width'];
        $billboard->dimensions_height = $input['dimensions_height'];
        $billboard->description = $input['description'];

        $billboard->save();
        return response (new BillboardResource($billboard))->setStatusCode(200);


    }

    /**
     * @param Billboard $billboard
     * @return BillboardResource
     *
     */

    public function show(Billboard $billboard)
    {
        return new BillboardResource($billboard);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Billboard  $billboard
     * @return \Illuminate\Http\Response
     */
    public function edit(Billboard $billboard)
    {
        //
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

        //todo find a way of converting psd to png
        $this->validate($request, [
            "display_duration" => "required",
            "location_lat" => "required",
            "location_long" => "required",
            "placement" => "required",
            "billboard_picture" => "required|mimes:jpg,jpeg,png,bmp,tiff |max:4096",
            "average_daily_views" => "required",
            "definition" => "required",
            "dimensions_width" => "required",
            "dimensions_height" => "required",
            "description" => "required",
        ]);

        $input = $request->all();

        $billboard = Billboard::find($id);
        $now = strtotime(date("h:i:sa"));

        $billboard->display_duration = $input['display_duration'];
        $billboard->location_lat = $input['location_lat'];
        $billboard->location_long = $input['location_long'];
        $billboard->placement = $input['placement'];
        $billboard_picture_ext=$request->file('billboard_picture')->getClientOriginalExtension();
        $billboard_picture_file = $request->file('billboard_picture');
        $billboard_picture_file_name= 'bb'.$request->location_lat.$now.'.'.$billboard_picture_ext;
        Storage::disk('local')->putFileAs('public/billboards',$billboard_picture_file,$billboard_picture_file_name);
        $billboard->billboard_picture = env('APP_APP_URL').'billboards/'.$billboard_picture_file_name;
        $billboard->average_daily_views = $input['average_daily_views'];
        $billboard->definition = $input['definition'];
        $billboard->dimensions_width = $input['dimensions_width'];
        $billboard->dimensions_height = $input['dimensions_height'];
        $billboard->description = $input['description'];

        $billboard->save();
        return response (new BillboardResource($billboard))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        //get image
        $picture= Billboard::find($id)->billboard_picture;
        //get rid of teh media serve url
        $new_pic= str_replace(env('MEDIA_SERVER_URL').'billboards/',"",$picture);
        //delete image
        File::delete(env('MEDIA_SERVER_FOLDER').'billboards/'.$new_pic);
        Billboard::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }
}
