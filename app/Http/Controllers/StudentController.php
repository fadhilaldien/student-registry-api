<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Student;
use App\Imports\StudentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Student::paginate(10);
        return response([ 'student' =>
        $student,
        'message' => 'General Success'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // CHECK FILE
        if($request->hasFile('file')){
            // IMPORT EXCEL AND DIRECT TO EXCEL IMPORT
            Excel::import(new StudentImport,
            $request->file('file')->store('files'));
        }else{
            // INPUT MANUAL
            $data = $request->all();
            $data['created_at'] = date('Y-m-d H:i:s');

            $validator = Validator::make($data, [
                'name' => 'required|max:255',
                'email' => 'email|required',
                'address' => 'required|max:255',
                'study_course' => 'required|max:255'
            ]);

            if($validator->fails()){
                return response(['error' => $validator->errors(),
                'General Error']);
            }

            $student = Student::create($data);
        }

        return response([ 'messages' => 'General Success'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Student $student)
    {
        return response([ 'student' => new
        StudentResource($student), 'message' => 'General Success'], 200);
    }

    public function search(Request $request)
    {
        // SEARCH WHERE LIKE TEXT OR STRING
        $student = Student::where('name', 'like', '%'.$request->search.'%')
                    ->orwhere('email',  'like', '%'.$request->search.'%')->paginate(10);

        return response([ 'student' => $student, 'message' => 'General Success'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $student = Student::where('id', $request->id)->update($request->all());

        return response([ 'student' => new
        StudentResource($student), 'message' => 'General Success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return response(['message' => 'General Success']);
    }
}

