<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleCollection;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\ScheduleTimesCollection;
use App\Http\Resources\ScheduleTimesResource;
use App\Schedule;
use App\ScheduleTimes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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



    public function store(Request $request)
    {

        $schedule_times = $request->schedule;


        $schedule = new Schedule();
        $schedule->save();
        $schedule_id=$schedule->id;

        foreach ($schedule_times as &$schedule_time){
            $schedule_time['schedule_id']=$schedule_id;
        }
        ScheduleTimes::insert($schedule_times);
        return new ScheduleResource($schedule);


    }

    /**
     * @param Schedule $schedule
     * @return ScheduleResource
     */

    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule);
    }


    /**
     * @param Request $request
     * @param $id
     * @return ScheduleResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $schedule = Schedule::findOrFail($id);
        }
        catch (ModelNotFoundException $e){
            return $this->ErrorReporter('Schedule Not Found', 'Schedule Id passed was not found in the database',422);
        }
        $all_schedule_ids = $schedule->ScheduleTimes()->get()->pluck('id');

        //delete this entries to reduce the complexity
        ScheduleTimes::destroy($all_schedule_ids);

        // create again
        $schedule_times = $request->schedule;


        foreach ($schedule_times as &$schedule_time){
            $schedule_time['schedule_id']=$id;
        }
        //insert using mass assignment
        $complete_schedule_times = ScheduleTimes::insert($schedule_times);

        return new ScheduleResource($schedule);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
        }
        catch (ModelNotFoundException $e){
            return $this->ErrorReporter('Schedule Not Found', 'Schedule Id passed was not found in the database',422);
        }
        $all_schedule_ids = $schedule->ScheduleTimes()->get()->pluck('id');

        //delete all related times
        ScheduleTimes::destroy($all_schedule_ids);
        //delete the actual schedule
        Schedule::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }


}
