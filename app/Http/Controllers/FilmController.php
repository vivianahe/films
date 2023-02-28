<?php

namespace App\Http\Controllers;

use App\Film;
use App\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        switch ($_GET['Q']) {
            case 0:
                $sql = Gender::where('state', 'active')->get();
                break;
            case 1:
                $sql = Film::join('users','users.id','=','films.user_id')
                    ->join('genders','genders.id','=','films.gender_id')
                    ->select('films.*','users.name as user', 'genders.name as gender')
                    ->get();
                break;
            case 2:
                $sql = Film::where('id', $_GET['film_id'])->first();
                break;
            case 3:
                $sql = Film::where('name', $_GET['name'])->exists();
                break;
        }
        return response()->json($sql);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->film_id==''){
            $state = 'active';
        } else {
            $state = $request->state;
        }
        $sql = Film::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'name' => $request->name,
                'productor' => $request->productor,
                'hour' => $request->hour,
                'minute' => $request->minute,
                'state' => $state,
                'gender_id' => $request->gender,
                'user_id' => Auth::user()->id
            ]
        );

        $anex = Film::where('img_film',$request->img_film)->exists();
        $file_anex = $request->file('img_film');
        if($anex==true){
            $name_file = $request->img_film;
        } else {
            $rutDel = Film::where('id', $sql->id)->first();
            if ($rutDel->img_film != null) {
                unlink(config('app.film') . $rutDel->img_film);
            }
            $name_file = $sql->id . '_img_film.' . $file_anex->getClientOriginalExtension();
            Storage::disk('film')->put($name_file, file_get_contents($file_anex->getRealPath()));
        }
        Film::where('id', $sql->id)->update([
            'img_film' => $name_file,
        ]);
        return response()->json($sql);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $sql = Film::where('id',$request->id)->update([
            'state' => $request->state
        ]);
        return response()->json($sql);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
