<?php

namespace App\Http\Controllers;

use App\Enum\VehicleRegisterStatus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicle = Vehicle::all()->where('isApprove', VehicleRegisterStatus::PENDING);
        return response(['data' => $vehicle]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'max:255', Rule::unique('vehicles', 'userId')],
            'first_name_kh' => ['required',  'max:100'],
            'last_name_kh' => ['required',  'max:100'],
            'first_name' => ['required',  'max:100'],
            'last_name' => ['required',  'max:100'],
            'role' => ['required', 'max:100'],
            'entity_name' => ['required', 'max:100'],
            'phone_number' => ['required', 'regex:/^(0[1-9]{2,2})[0-9]{6,7}$/', Rule::unique('vehicles', 'phoneNumber')],
            'email' => ['required', 'email', Rule::unique('vehicles', 'email')],
            'address' => ['required', 'max:255'],
            'vehicle_release_year' => ['required'],
            'vehicle_license_plate' => ['required', 'max:100', Rule::unique('vehicles', 'vehicleLicensePlate')],
            'vehicle_model' => ['required', 'max:100'],
            'vehicle_color' => ['required', 'max:100'],
            'is_approve' => ['required', 'regex:/^(1|2|3){1}$/'],
            'description' => ['max:255'],
            'img' => ['required', 'image', 'max:5024'],

        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        DB::beginTransaction();
        try {
            if ($request->hasFile('img')) {

                $file = $request->file('img');
                $imageNameWithExt = $file->getClientOriginalName();
                $img = time() . $imageNameWithExt;
                $file->move('img/', $img);
            }

            DB::table('vehicles')->insert([
                'userId' => $request->user_id,
                'firstNameKh' => $request->first_name_kh,
                'lastNameKh' => $request->last_name_kh,
                'firstName' => $request->first_name,
                'lastName' => $request->last_name,
                'role' => $request->role,
                'entityName' => $request->entity_name,
                'phoneNumber' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'vehicleReleaseYear' => $request->vehicle_release_year,
                'vehicleLicensePlate' => $request->vehicle_license_plate,
                'vehicleModel' => $request->vehicle_model,
                'vehicleColor' => $request->vehicle_color,
                'description' => $request->description,
                'isApprove' => $request->is_approve,
                'img' => $img,
                'created_at' => Carbon::now()->format('Y-m-d')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('An error occurred while processing your request');
        }

        return $this->responseSuccess();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $registerId)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $registerId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $registerId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => [
                'required', 'max:255',
                Rule::unique('vehicles', 'userId')
                    ->whereNot('id', $registerId)
            ],
            'first_name_kh' => ['required',  'max:100'],
            'last_name_kh' => ['required',  'max:100'],
            'first_name' => ['required',  'max:100'],
            'last_name' => ['required',  'max:100'],
            'role' => ['required', 'max:100'],
            'entity_name' => ['required', 'max:100'],
            'phone_number' => [
                'required',
                'regex:/^(0[1-9]{2,2})[0-9]{6,7}$/',
                Rule::unique('vehicles', 'phoneNumber')
                    ->whereNot('id', $registerId)
            ],
            'email' => [
                'required', 'email',
                Rule::unique('vehicles', 'email')
                    ->whereNot('id', $registerId)
            ],
            'address' => ['required', 'max:255'],
            'vehicle_release_year' => ['required'],
            'vehicle_license_plate' => [
                'required', 'max:100',
                Rule::unique('vehicles', 'vehicleLicensePlate')
                    ->whereNot('id', $registerId)
            ],
            'vehicle_model' => ['required', 'max:100'],
            'vehicle_color' => ['required', 'max:100'],
            'is_approve' => ['required', 'regex:/^(0|1){1}$/'],
            'description' => ['max:255'],
            'img' => ['image', 'max:5024'],

        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $vehicle = DB::table('vehicles')->where('id', $registerId)->first();

        if ($request->hasFile('img')) {

            // return 'new img and delete old img';
            $file = $request->file('img');
            $imageNameWithExt = $file->getClientOriginalName();
            $img = time() . $imageNameWithExt;
            $file->move('img/', $img);
            unlink('img/' . $vehicle->img);
        } else {

            $img = $vehicle->img;
        }


        DB::beginTransaction();

        try {

            DB::table('vehicles')
                ->where('id', $registerId)
                ->update([
                    'userId' => $request->user_id,
                    'firstNameKh' => $request->first_name_kh,
                    'lastNameKh' => $request->last_name_kh,
                    'firstName' => $request->first_name,
                    'lastName' => $request->last_name,
                    'role' => $request->role,
                    'entityName' => $request->entity_name,
                    'phoneNumber' => $request->phone_number,
                    'email' => $request->email,
                    'address' => $request->address,
                    'vehicleReleaseYear' => $request->vehicle_release_year,
                    'vehicleLicensePlate' => $request->vehicle_license_plate,
                    'vehicleModel' => $request->vehicle_model,
                    'vehicleColor' => $request->vehicle_color,
                    'description' => $request->description,
                    'isApprove' => $request->is_approve,
                    'img' => $img
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('An error occurred while processing your request');
        }

        return ['message' => 'Update successfully'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $registerId)
    {
        return 'delete';
    }
}
