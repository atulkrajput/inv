<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Model\Customer;
use Image;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $customer = Customer::withCount('sale')->latest()->paginate(15);
       return response()->json($customer);
    }

    public function findCustomer(){
        if($search = \Request::get('q')) {
            $customer = Customer::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->withCount('sale')->latest()->paginate(15);
        }
        else{
            $customer = Customer::withCount('sale')->latest()->paginate(15);
        }

        return $customer;
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
            'name' => 'required|string|max:190',
            'mobile' => 'required|numeric|digits:10|unique:customers'
        ]);
        if(!isset($request['promotions'])):$request['promotions'] = 0; endif;
        return Customer::create([
            'name' => $request['name'],
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'address' => $request['address'],
            'promotions' => $request['promotions']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $customer = DB::table('customers')->where('id',$id)->first();
       return response()->json($customer);
    }

     

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Customer::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'mobile' => 'required|numeric|digits:10|unique:customers,mobile,'.$user->id
        ]);
        $user->update($request->all());
        return ['message' => 'updatng data'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customers::findOrFail($id);
        $customer->delete();
        return ['message' => 'record deleted'];
    }


}
