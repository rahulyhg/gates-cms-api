<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Sheet;
class SheetController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(Sheet::all());
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
        'data' => 'required',
        'type' => 'required'
         ]);

        $sheet = new Sheet();

        $sheet->data = $request->input('data');
        $sheet->type = $request->input('type');

        $sheet->save();
        return response()->json($sheet, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return response()->json(Sheet::find($id));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Sheet::find($id)->delete();
      return response('', 200);
    }




}