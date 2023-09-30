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

    public function index(Request $request)
    {
        $query = DB::table('vehicles')->select([
            'id',
            'userId',
            'firstNameKh',
            'lastNameKh',
            'firstName',
            'lastName',
            'role',
            'entityName',
            'phoneNumber',
            'email',
            'address',
            'vehicleReleaseYear',
            'vehicleLicensePlate',
            'vehicleModel',
            'vehicleColor',
            'description',
            'isApprove',
            'img'
        ]);

        if ($request->is_approve) {
            $query->where('isApprove', $request->is_approve);
        } else {
            $query->where('isApprove', VehicleRegisterStatus::APPROVE);
        }

        $vehicle = $query->get();

        return response(['data' => $vehicle]);
    }

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
                'created_at' => Carbon::now()->format('Y-m-d h:i:s')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('An error occurred while processing your request');
        }

        return $this->responseSuccess();
    }

    public function show(string $registerId)
    {
        $vehicle = DB::table('vehicles')->where('id', $registerId)->first();
        return response(['data' => $vehicle]);
    }

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
            'is_approve' => ['required', 'regex:/^(1|2|3){1}$/'],
            'description' => ['max:255'],
            'img' => ['image', 'max:5024'],

        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $vehicle = DB::table('vehicles')->where('id', $registerId)->first();

        if ($request->hasFile('img')) {
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
                    'img' => $img,
                    'updated_at' => Carbon::now()->format("Y-m-d h:i:s")
                ]);

            return [
                'is_approve' => $request->is_approve,
                'description' => $request->description
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('An error occurred while processing your request');
        }
    }

    public function updateIsAprrove(Request $request, string $registerId)
    {

        $validator = Validator::make($request->all(), [
            'is_approve' => ['required', 'regex:/^(1|2|3){1}$/'],
            'description' => ['max:255'],
        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $vehicle = Vehicle::find($registerId);
        if ($vehicle) {

            $vehicle->update([
                'isApprove' => $request->is_approve,
                'description' => $request->description,
                'updated_at' => Carbon::now()->format("Y-m-d h:i:s")
            ]);

            return [
                'is_approve' => $request->is_approve,
                'description' => $request->description
            ];
        }
        return ['message' => 'Id does not exist'];
    }

    public function destroy(string $registerId)
    {
        $vehicle = Vehicle::find($registerId);
        if ($vehicle) {
            unlink('img/' . $vehicle->img);
            $vehicle->delete();
            return ['message' => 'Delete succesfully'];
        }
        return ['message' => 'Id does not exist'];
    }
}
