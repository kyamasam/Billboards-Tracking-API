<?php

namespace App\Http\Controllers;

use App\Budget;
use App\Http\Resources\BudgetCollection;
use App\Http\Resources\BudgetResource;
use Illuminate\Http\Request;
use App\Traits\BaseTraits;
class BudgetController extends Controller
{
    use BaseTraits;
    /**
     * @return BudgetCollection
     *
     */
    public function index()
    {
        //anyone can access this
        return new BudgetCollection(Budget::paginate());

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
            "total_animation_cost" => "required|numeric",
            "total_campaign_cost" => "required|numeric",
            "final_cost" => "required|numeric",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
        ]);
        //check that the end date is not less than start date
        //convert to DateTime
        $input = $request->all();
        $start_date = new \DateTime($input['start_date']);
        $end_date = new \DateTime($input['end_date']);
        $today = new \DateTime(date('Y-m-d'));

        if(!($start_date <= $end_date)){
            return $this->ErrorReporter('Invalid Data','The End Date Must be equal to or greater than the start date', 422);

        }elseif (!($today<=$start_date)){
            return $this->ErrorReporter('Invalid Data','The Start Date Must be Later than or equal to today', 422);
        }




        $budget = new Budget();
        $budget->total_expenditure= $input['total_expenditure'];
        $budget->start_date= $input['start_date'];
        $budget->end_date= $input['end_date'];


        $budget->save();
        return response (new BudgetResource($budget))->setStatusCode(201);


    }

    /**
     * @param BudgetResource $budget
     * @return BudgetResource
     *
     */

    public function show(Budget $budget)
    {
        return new BudgetResource($budget);
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
            "total_animation_cost" => "required|numeric",
            "total_campaign_cost" => "required|numeric",
            "final_cost" => "required|numeric",
            "start_date" => "required|date_format:Y-m-d",
            "end_date" => "required|date_format:Y-m-d",
        ]);


        //check that the end date is not less than start date
        //convert to DateTime
        $input = $request->all();
        $start_date = new \DateTime($input['start_date']);
        $end_date = new \DateTime($input['end_date']);
        $today = new \DateTime(date('Y-m-d'));

        if(!($start_date <= $end_date)){
            return $this->ErrorReporter('Invalid Data','The End Date Must be equal to or greater than the start date', 422);

        }elseif (!($today<=$start_date)){
            return $this->ErrorReporter('Invalid Data','The Start Date Must be Later than or equal to today', 422);
        }




        $budget = Budget::find($id);
        $budget->total_expenditure= $input['total_animation_cost'];
        $budget->total_expenditure= $input['total_campaign_cost'];
        $budget->total_expenditure= $input['final_cost'];
        $budget->start_date= $input['start_date'];
        $budget->end_date= $input['end_date'];


        $budget->save();
        return response (new BudgetResource($budget))->setStatusCode(200);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        Budget::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }



}
