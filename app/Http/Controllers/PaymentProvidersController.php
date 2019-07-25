<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentProvidersCollection;
use App\Http\Resources\PaymentProvidersResource;
use App\PaymentProvider;
use App\Traits\BaseTraits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PaymentProvidersController extends Controller
{

    use BaseTraits;

    /**
     * @return PaymentProvidersCollection
     */
    public function index()
    {
        //anyone can access this
        return new PaymentProvidersCollection(PaymentProvider::paginate());
    }

    /**
     * @param Request $request
     * @return PaymentProvidersResource
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request)
    {
        $this->validate($request, [
            "provider_name" => "required",
        ]);

        $paymentProvider = new PaymentProvider();
        $paymentProvider->provider_name = $request->provider_name;
        $paymentProvider->save();

        return new PaymentProvidersResource($paymentProvider);


    }

    /**
     * @param PaymentProvider $paymentProvider
     * @return PaymentProvidersResource
     */

    public function show(PaymentProvider $paymentProvider)
    {
        return new PaymentProvidersResource($paymentProvider);
    }


    /**
     * @param Request $request
     * @param $id
     * @return PaymentProvidersResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $paymentProvider = PaymentProvider::findOrFail($id);
        }
        catch (ModelNotFoundException $e){
            return $this->ErrorReporter('Payment Provider Not Found', 'Payment Provider Id passed was not found in the database',422);
        }

        $paymentProvider->provider_name = $request->provider_name;
        $paymentProvider->save();


        return new PaymentProvidersResource($paymentProvider);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     */


    public function destroy($id)
    {
        try {
            PaymentProvider::findOrFail($id);
        }
        catch (ModelNotFoundException $e){
            return $this->ErrorReporter('Payment Provider Not Found', 'Payment Provider Id passed was not found in the database',422);
        }
        PaymentProvider::destroy($id);
        return $this->SuccessReporter('Record Deleted', 'Record was successfully deleted',200);
    }
}
