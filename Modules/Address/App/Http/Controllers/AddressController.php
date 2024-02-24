<?php

namespace Modules\Address\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Address\App\Http\Requests\AddressRequest;
use Modules\Address\App\Http\Resources\AddressResource;
use Modules\Address\App\Models\Address;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // return view('user::index');
        $user = $request->user();
        $addresses = AddressResource::collection($user->addresses);

        return response()->json(['message' => 'Success', 'addresses' => $addresses], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('address::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = $request->user();

        $address = $user->addresses()->create([
            'name' => $request->name,
            'city' => $request->city,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'country' => $request->country,
            'address' => $request->address,
            'primary' => $request->primary,
            'zip_code' => $request->zip_code,
            'address_type' => $request->address_type,
        ]);

        return response()->json(['message' => 'Success', 'address' => new AddressResource($address)], Response::HTTP_OK);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // return view('address::show');
        $address = Address::where('id', $id)->firstOrFail();

        return response()->json(['message' => 'Success', 'address' => $address], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('address::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $address = Address::find($id);

        $address->name = $request->name;
        $address->city = $request->city;
        $address->email = $request->email;
        $address->phone = $request->phone;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->address = $request->address;
        $address->primary = $request->primary;
        $address->zip_code = $request->zip_code;
        $address->address_type = $request->address_type;

        $address->save();

        return response()->json(['message' => 'Success', 'address' => new AddressResource($address)], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        Address::destroy($id);
    }
}
