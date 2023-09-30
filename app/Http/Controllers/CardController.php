<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Enum\RegisterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('cards')->select([
            'id',
            'firstNameKh',
            'lastNameKh',
            'firstName',
            'lastName',
            'role',
            'entityName',
            'areaCode',
            'phoneNumber',
            'address',
            'description',
            'isApprove',
            'img'
        ]);

        if ($request->is_approve) {
            $query->where('isApprove', $request->is_approve);
        }

        $cards = $query->get();

        return response(['data' => $cards]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'first_name_kh' => ['required',  'max:100'],
            'last_name_kh' => ['required',  'max:100'],
            'first_name' => ['required',  'max:100'],
            'last_name' => ['required',  'max:100'],
            'role' => ['required', 'max:100'],
            'entity_name' => ['required', 'max:100'],
            'area_code' => ['required', 'max:100'],
            'phone_number' => [
                'required',
                'regex:/^(0[1-9]{2,2})[0-9]{6,7}$/',
                Rule::unique('cards', 'phoneNumber')
            ],
            'address' => ['required', 'max:255'],
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

            DB::table('cards')->insert([
                'firstNameKh' => $request->first_name_kh,
                'lastNameKh' => $request->last_name_kh,
                'firstName' => $request->first_name,
                'lastName' => $request->last_name,
                'role' => $request->role,
                'entityName' => $request->entity_name,
                'areaCode' => $request->area_code,
                'phoneNumber' => $request->phone_number,
                'address' => $request->address,
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

    public function show(string $cardId)
    {
        $card = DB::table('cards')->where('id', $cardId)->first();
        if ($card) {
            return response(['data' => $card]);
        }
        return response(['message' => 'Id does not Exist']);
    }

    public function update(Request $request, string $cardId)
    {
        $validator = Validator::make($request->all(), [
            'first_name_kh' => ['required',  'max:100'],
            'last_name_kh' => ['required',  'max:100'],
            'first_name' => ['required',  'max:100'],
            'last_name' => ['required',  'max:100'],
            'role' => ['required', 'max:100'],
            'entity_name' => ['required', 'max:100'],
            'area_code' => ['required', 'max:100'],
            'phone_number' => [
                'required',
                'regex:/^(0[1-9]{2,2})[0-9]{6,7}$/',
                Rule::unique('cards', 'phoneNumber')
                    ->whereNot('id', $cardId)
            ],
            'address' => ['required', 'max:255'],
            'is_approve' => ['required', 'regex:/^(1|2|3){1}$/'],
            'description' => ['max:255'],
            'img' => ['image', 'max:5024'],

        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $card = DB::table('cards')->where('id', $cardId)->first();

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $imageNameWithExt = $file->getClientOriginalName();
            $img = time() . $imageNameWithExt;
            $file->move('img/', $img);
            unlink('img/' . $card->img);
        } else {
            $img = $card->img;
        }

        DB::beginTransaction();

        try {

            DB::table('cards')
                ->where('id', $cardId)
                ->update([
                    'firstNameKh' => $request->first_name_kh,
                    'lastNameKh' => $request->last_name_kh,
                    'firstName' => $request->first_name,
                    'lastName' => $request->last_name,
                    'role' => $request->role,
                    'entityName' => $request->entity_name,
                    'areaCode' => $request->area_code,
                    'phoneNumber' => $request->phone_number,
                    'address' => $request->address,
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

    public function updateIsAprrove(Request $request, string $cardId)
    {

        $validator = Validator::make($request->all(), [
            'is_approve' => ['required', 'regex:/^(1|2|3){1}$/'],
            'description' => ['max:255'],
        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $card = Card::find($cardId);

        if ($card) {

            $card->update([
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

    public function destroy(string $cardId)
    {
        $card = Card::find($cardId);

        if ($card) {
            unlink('img/' . $card->img);
            $card->delete();
            return ['message' => 'Delete succesfully'];
        }
        return ['message' => 'Id does not exist'];
    }
}
