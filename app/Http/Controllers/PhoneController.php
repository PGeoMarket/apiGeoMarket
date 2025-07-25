<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()  {
        $phone=Phone::all();

        return response()->json($phone);
    }
    /* public function create()  {
        $areas = Area::all();
        $trainingCenters = TrainingCenter::all();

        return view('teacher.create',compact('areas', 'trainingCenters'));
    } */

    public function store(Request $request)  {
        $phone=Phone::create($request->all());

        return response()->json($phone);
    }

    public function show($id) {
        $phone = Phone::find($id);

        return response()->json($phone);
    }

    /* public function update(Request $request,Teacher $teacher) {

        $teacher->update($request->all());

        return redirect()->route('teacher.index');

    } */

    /* public function edit(Teacher $teacher) {
        return view('teacher.edit',compact('teacher'));
    } */

    public function destroy(Phone $phone) {
        $phone->delete();

        return response()->json($phone);
    }
}
