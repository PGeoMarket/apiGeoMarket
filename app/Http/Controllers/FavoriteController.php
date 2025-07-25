<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()  {
        $favorites=Favorite::all();

        return response()->json($favorites);
    }
    /* public function create()  {
        $areas = Area::all();
        $trainingCenters = TrainingCenter::all();

        return view('teacher.create',compact('areas', 'trainingCenters'));
    } */

    public function store(Request $request)  {
        $favorite=Favorite::create($request->all());

        return response()->json($favorite);
    }

    public function show($id) {
        $favorite = Favorite::find($id);

        return response()->json($favorite);
    }

    /* public function update(Request $request,Teacher $teacher) {

        $teacher->update($request->all());

        return redirect()->route('teacher.index');

    } */

    /* public function edit(Teacher $teacher) {
        return view('teacher.edit',compact('teacher'));
    } */

    public function destroy(Favorite $favorite) {
        $favorite->delete();

        return response()->json($favorite);
    }
}
