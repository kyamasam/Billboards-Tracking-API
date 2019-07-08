<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleCollection;
use App\Http\Resources\ScheduleResource;
use App\Schedule;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
class ScheduleController extends Controller
{
     use BaseTraits;
    /**
     * @return ScheduleCollection
     *
     */
    public function index()
    {
        //anyone can access this
        return new ScheduleCollection(Schedule::paginate());

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
            "schedule_day" => "required",
            "schedule_time" => "required",
        ]);



        $input = $request->all();

        //verify length

        $schedule_day_array = explode(',', $input['schedule_day']);
        $schedule_time_array = explode(',', $input['schedule_time']);

        if(count($schedule_day_array) != count($schedule_time_array)){
            return $this->ErrorReporter('The given data was invalid', 'The number of schedule days must match the number of schedule times' , 422);
        }



        $schedule = new Schedule();
        $schedule->schedule_day = $input['schedule_day'];
        $schedule->schedule_time = $input['schedule_time'];


        $schedule->save();
        return response (new ScheduleResource($schedule))->setStatusCode(200);


    }

    /**
     * @param Schedule $schedule
     * @return ScheduleResource
     *
     */

    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule);
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
            "schedule_day" => "required",
            "schedule_time" => "required",
        ]);

        $input = $request->all();

        $schedule_day_array = explode(',', $input['schedule_day']);
        $schedule_time_array = explode(',', $input['schedule_time']);

        if(count($schedule_day_array) != count($schedule_time_array)){
            return $this->ErrorReporter('The given data was invalid', 'The number of schedule days must match the number of schedule times' , 422);
        }

        $schedule = Schedule::find($id);

        $schedule->schedule_day = $input['schedule_day'];
        $schedule->schedule_time = $input['schedule_time'];

        $schedule->save();
        return response (new ScheduleResource($schedule))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        Schedule::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }


}
