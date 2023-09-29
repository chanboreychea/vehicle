<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Enum\RegisterType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegisterExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{

    public function internationalRegister(Request $request)
    {
        $student = $request->all();
        $validator = Validator::make($request->all(), [
            'register_type' => ['nullable', 'regex:/^(2){1}$/'],
            'identify_type' => ['nullable', 'regex:/^(1|2|3){1}$/'],
            'semester' => ['nullable'],
            'about' => ['max:100'],
            'first_name' => ['required',  'max:100'],
            'last_name' => ['required',  'max:100'],
            'phone_number' => ['required', 'regex:/^(0[1-9]{2,2})[0-9]{6,7}$/'],
            'gender' => ['required', 'regex:/^(m|f){1}$/'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'isPermernant' => ['nullable',  'max:255'],
            'current_address' => ['required',  'max:255'],
            'permanent_address' => ['required',  'max:255'],
            'emergency_phone_number' => ['required', 'max:100'],
            'emergency_address' => ['nullable',  'max:100'],
            'emergency_name' => ['required',  'max:100'],
            'emergency_relationship' => ['nullable',  'max:100'],
            'emergency_email' => ['nullable',  'email', 'max:100'],
            'exam_certificate' => ['required', 'image',  'max:5024'],
            'old_uni_credit' => ['nullable', 'image',  'max:5024'],
            'english_proficiency' => ['required', 'image', 'max:5024'],
            'high_school_diploma' => ['nullable', 'image', 'max:5024'],
            'high_school_transcript' => ['nullable', 'image', 'max:5024'],
            'nat_highschool' => ['nullable', 'min:1', 'max:3'],
            'nat_graduated_year' => ['nullable', 'min:1', 'max:5'],
            'nat_grade' => ['nullable'],
            'nat_total_score' => ['nullable',],
            'nat_exam_year' => ['nullable'],
            'nat_exam_seat' => ['nullable'],
            'nat_exam_center' => ['nullable', 'min:1', 'max:100'],
            'major_preference' => ['required'],
            'isLocal' => ['nullable'],
            'high_school_name' => ['nullable',  'max:100'],
            'country' => ['nullable',  'max:100'],
            'old_uni_name' => ['nullable',  'max:100'],
            'old_uni_country' => ['nullable',  'max:100'],
            'old_uni_major' => ['nullable',  'max:100'],
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'id_img' => ['nullable', 'image', 'max:5024'],
            'img' => ['required', 'image', 'max:5024'],
            'student_type' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        DB::beginTransaction();
        // try {

        $studentImg = $request->hasFile('img') ? Str::random(10) . '.' . $request->file('img')->getClientOriginalExtension() : null;
        $idImg = $request->hasFile('id_img') ? Str::random(10) . '.' . $request->file('id_img')->getClientOriginalExtension() : null;
        $examCertificate = $request->hasFile('exam_certificate') ? Str::random(10) . '.' . $request->file('exam_certificate')->getClientOriginalExtension() : null;
        $highSchoolDiploma = $request->hasFile('high_school_diploma') ? Str::random(10) . '.' . $request->file('high_school_diploma')->getClientOriginalExtension() : null;
        $highSchoolTranscript = $request->hasFile('high_school_transcript') ? Str::random(10) . '.' . $request->file('high_school_transcript')->getClientOriginalExtension() : null;
        $englishProficient = $request->hasFile('english_proficiency') ? Str::random(10) . '.' . $request->file('english_proficiency')->getClientOriginalExtension() : null;

        if ($request->hasFile('english_proficiency')) {

            $file = $request->file('english_proficiency');
            $imageNameWithExt = $file->getClientOriginalName();
            $englishProficient = time() . $imageNameWithExt;
            $file->move('img/', $englishProficient);

            //$englishProficient = Str::random(10) . '.' . $request->english_proficiency->getClientOriginalExtension();
            //Storage::disk('public')->put($englishProficient, file_get_contents($request->english_proficiency));
        }

        if ($request->hasFile('img')) {

            $file = $request->file('img');
            $imageNameWithExt = $file->getClientOriginalName();
            $studentImg = time() . $imageNameWithExt;
            $file->move('img/', $studentImg);

            //$studentImg = Str::random(10) . '.' . $request->img->getClientOriginalExtension();
            //Storage::disk('public')->put($studentImg, file_get_contents($request->img));
        }

        if ($request->hasFile('exam_certificate')) {

            $file = $request->file('exam_certificate');
            $imageNameWithExt = $file->getClientOriginalName();
            $examCertificate = time() . $imageNameWithExt;
            $file->move('img/', $examCertificate);

            //$examCertificate = Str::random(10) . '.' . $request->exam_certificate->getClientOriginalExtension();
            //Storage::disk('public')->put($examCertificate, file_get_contents($request->exam_certificate));
        }

        if ($request->hasFile('id_img')) {

            $file = $request->file('id_img');
            $imageNameWithExt = $file->getClientOriginalName();
            $idImg = time() . $imageNameWithExt;
            $file->move('img/', $idImg);

            //$idImg = Str::random(10) . '.' . $request->id_img->getClientOriginalExtension();
            //Storage::disk('public')->put($idImg, file_get_contents($request->id_img));
        }
        if ($request->hasFile('high_school_diploma')) {

            $file = $request->file('high_school_diploma');
            $imageNameWithExt = $file->getClientOriginalName();
            $highSchoolDiploma = time() . $imageNameWithExt;
            $file->move('img/', $highSchoolDiploma);

            //$highSchoolDiploma = Str::random(10) . '.' . $request->high_school_diploma->getClientOriginalExtension();
            //Storage::disk('public')->put($highSchoolDiploma, file_get_contents($request->high_school_diploma));
        }

        if ($request->hasFile('high_school_transcript')) {

            $file = $request->file('high_school_transcript');
            $imageNameWithExt = $file->getClientOriginalName();
            $highSchoolTranscript = time() . $imageNameWithExt;
            $file->move('img/', $highSchoolTranscript);

            //$highSchoolTranscript = Str::random(10) . '.' . $request->high_school_transcript->getClientOriginalExtension();
            //Storage::disk('public')->put($highSchoolTranscript, file_get_contents($request->high_school_transcript));
        }

        DB::table('schools')->insert([
            'registerType' => $request->register_type,
            'identifyType' => $request->identify_type,
            'semester' => $request->semester,
            'about' => $request->about,
            'firstName' => $request->first_name,
            'lastName' => $request->last_name,
            'phoneNumber' => $request->phone_number,
            'gender' => $request->gender,
            'dateOfBirth' => $request->date_of_birth,
            'email' => $request->email,
            'isPermernant' => $request->isPermernant,
            'currentAddress' => $request->current_address,
            'permanentAddress' => $request->permanent_address,
            'emergencyPh' => $request->emergency_phone_number,
            'emergencyEmail' => $request->emergency_email,
            'emergencyAddress' => $request->emergency_address,
            'emergencyName' => $request->emergency_name,
            'emergencyRelationship' => $request->emergency_relationship,
            'img' => $studentImg,
            'natHighschool' => $request->nat_highschool,
            'natGrade' => $request->nat_grade,
            'natGraduatedYear' => $request->nat_graduated_year,
            'natExamYear' => $request->nat_exam_year,
            'natExamSeat' => $request->nat_exam_seat,
            'natExamCenter' => $request->nat_exam_center,
            'majorPreference' => $request->major_preference,
            'examCertificate' => $examCertificate,
            'englishProficient' => $englishProficient,
            'highSchoolDiploma' => $highSchoolDiploma,
            'highSchoolTranscript' => $highSchoolTranscript,
            'isLocal' => $request->isLocal,
            'natTotalScore' => $request->nat_total_score,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'highSchoolName' => $request->high_school_name,
            'country' => $request->country,
            'oldUniName' => $request->old_uni_name,
            'oldUniCountry' => $request->old_uni_country,
            'oldUniMajor' => $request->old_uni_major,
            'oldUniCredit' => $request->old_uni_credit,
            'idImg' => $idImg,
            'studentType' => $request->student_type,
        ]);

        DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return $this->responseError('An error occurred while processing your request');
        // }

        return $this->responseSuccess();
    }

    public function allRegister(Request $request)
    {
        $studentRegister = DB::table('schools')
            ->select('firstName', 'phoneNumber', 'gender', 'dateOfBirth', 'img', 'registerType')->get();
        // $studentRegister = School::all();

        return response(['data' => $studentRegister]);
    }

    public function getRegister(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $registertypes = $request->input('registerTypes');
        $status = $request->input('status');
        $studenttype = $request->input('studenttype');

        $query = DB::table('schools')
            ->select('id', 'firstName', 'lastName', 'gender', 'dateOfBirth', 'registerType', 'status', 'created_at');

        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        }
        if ($registertypes) {
            $query->where('registerType', $registertypes);
        }
        if ($studenttype) {
            $query->where('studentType', $studenttype);
        }
        if ($status != null) {
            $query->where('status', $status);
        }

        $registers = $query->get();

        $allRegister = count($registers);

        return view('admin.register.index', compact('registers', 'allRegister'));
    }

    public function showRegisterById(string $id)
    {
        $registerType = DB::table('schools')->where('id', $id)->first();
        $register = School::all()->where('id', $id)->first();

        if ($registerType->registerType == RegisterType::NATIONAL) {
            return view('admin.register.showNational', compact('register'));
        } elseif ($registerType->registerType == RegisterType::INTERNATIONAL) {
            return view('admin.register.showInternational', compact('register'));
        } elseif ($registerType->registerType == RegisterType::TRANSFER) {
            return view('admin.register.showTransfer', compact('register'));
        }
    }

    public function editRegisterById(string $id)
    {
        $registerType = DB::table('schools')->where('id', $id)->first();
        $reDate = $registerType->created_at;
        $register = School::all()->where('id', $id)->first();
        if ($registerType->registerType == RegisterType::NATIONAL) {
            return view('admin.register.editNational', compact('register', 'reDate'));
        } elseif ($registerType->registerType == RegisterType::INTERNATIONAL) {
            return view('admin.register.editInternational', compact('register', 'reDate'));
        } elseif ($registerType->registerType == RegisterType::TRANSFER) {
            return view('admin.register.editTransfer', compact('register', 'reDate'));
        }
    }

    public function updatedRegisterById(Request $request, string $id)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'status' => 'required',
            'email' => 'required',
            'dateofbirth' => 'required',
            'address' => 'required',

            'majorpreference' => 'required',
            'registerdate' => 'required',

            'name' => 'required',
            'relationship' => 'required',
            'phonenumber' => 'required',
            'eemail' => 'required',
            'eaddress' => 'required',

            // 'olduniversitycountry' => 'required',
            // 'olduniversitymajor' => 'required',

        ]);

        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');
        $gender = $request->input('gender');
        $status = $request->input('status');
        $date = $request->input('dateofbirth');
        $email = $request->input('email');
        $address = $request->input('address');

        $majorpreference = $request->input('majorpreference');
        $registerdate = $request->input('registerdate');

        $name = $request->input('name');
        $relationship = $request->input('relationship');
        $phonenumber = $request->input('phonenumber');
        $eemail = $request->input('eemail');
        $eaddress = $request->input('eaddress');


        $registerType = DB::table('schools')->where('id', $id)->first();

        if ($registerType->registerType == RegisterType::NATIONAL) {

            DB::table('schools')
                ->where('id', $id)
                ->update([
                    'firstName' => $firstname,
                    'lastName' => $lastname,
                    'gender' => $gender,
                    'dateOfBirth' => $date,
                    'status' => $status,
                    'email' => $email,
                    'currentAddress' => $address,

                    'emergencyName' => $name,
                    'emergencyAddress' => $eaddress,
                    'emergencyEmail' => $eemail,
                    'emergencyPh' => $phonenumber,
                    'emergencyRelationship' => $relationship,

                    'majorPreference' => $majorpreference,
                    'created_at' => $registerdate
                ]);
        } elseif ($registerType->registerType == RegisterType::INTERNATIONAL) {

            DB::table('schools')
                ->where('id', $id)
                ->update([
                    'firstName' => $firstname,
                    'lastName' => $lastname,
                    'gender' => $gender,
                    'dateOfBirth' => $date,
                    'status' => $status,
                    'email' => $email,
                    'currentAddress' => $address,

                    'emergencyName' => $name,
                    'emergencyAddress' => $eaddress,
                    'emergencyEmail' => $eemail,
                    'emergencyPh' => $phonenumber,
                    'emergencyRelationship' => $relationship,

                    'majorPreference' => $majorpreference,
                    'created_at' => $registerdate
                ]);
        } else {

            $olduniversitycountry = $request->input('olduniversitycountry');
            $olduniversitymajor = $request->input('olduniversitymajor');

            DB::table('schools')
                ->where('id', $id)
                ->update([
                    'firstName' => $firstname,
                    'lastName' => $lastname,
                    'gender' => $gender,
                    'dateOfBirth' => $date,
                    'status' => $status,
                    'email' => $email,
                    'currentAddress' => $address,

                    'emergencyName' => $name,
                    'emergencyAddress' => $eaddress,
                    'emergencyEmail' => $eemail,
                    'emergencyPh' => $phonenumber,
                    'emergencyRelationship' => $relationship,

                    'oldUniCountry' => $olduniversitycountry,
                    'oldUniMajor' => $olduniversitymajor,

                    'majorPreference' => $majorpreference,
                    'created_at' => $registerdate
                ]);
        }

        return redirect('/admins/registers');
    }

    public function deleteRegisterById(string $id)
    {
        School::find($id)->delete();
        return redirect('/admins/registers');
    }

    public function exportRegister()
    {
        return Excel::download(new RegisterExport, 'registers.xlsx');
        // return (new RegisterExport)->forYear(2018)->download('invoices.xlsx');
    }
}
