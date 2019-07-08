<?php

namespace App\Http\Controllers;

use App\Billboard;
use App\Http\Resources\BillboardCollection;
use App\Http\Resources\BillboardResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            "billboard_picture" => "required",
            "average_daily_views" => "required",
            "definition" => "required",
            "dimensions_width" => "required",
            "dimensions_height" => "required",
            "description" => "required",
        ]);

        $input = $request->all();

        $billboard = new Billboard;
        $billboard->display_duration = $input['display_duration'];
        $billboard->location_lat = $input['location_lat'];
        $billboard->location_long = $input['location_long'];
        $billboard->placement = $input['placement'];
        $billboard->billboard_picture = $input['billboard_picture'];
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


        $this->validate($request, [
            "display_duration" => "required",
            "location_lat" => "required",
            "location_long" => "required",
            "placement" => "required",
            "billboard_picture" => "required",
            "average_daily_views" => "required",
            "definition" => "required",
            "dimensions_width" => "required",
            "dimensions_height" => "required",
            "description" => "required",
        ]);

        $input = $request->all();

        $billboard = Billboard::find($id);

        $billboard->display_duration = $input['display_duration'];
        $billboard->location_lat = $input['location_lat'];
        $billboard->location_long = $input['location_long'];
        $billboard->placement = $input['placement'];
        $billboard->billboard_picture = $input['billboard_picture'];
        $billboard->average_daily_views = $input['average_daily_views'];
        $billboard->definition = $input['definition'];
        $billboard->dimensions_width = $input['dimensions_width'];
        $billboard->dimensions_height = $input['dimensions_height'];
        $billboard->description = $input['description'];

        $billboard->save();
        return response (new BillboardResource($billboard))->setStatusCode(200);
    }

    // todo - add file uploads for billboard images

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        Billboard::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }
}
