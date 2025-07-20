<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::all();

        $brandsHasCars = Brand::has("cars")->get();

        $brandCollection = collect($brandsHasCars->all())->sortBy("brand_name");

        return response()->json(  $brandCollection->values()->all() );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brands = config("data.brands");

        foreach ($brands as $brand) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brand)));
            $imgUrl = "brands/" . $slug . "-logo.png";
            Brand::updateOrCreate(
                ["brand_name" => $brand],
                ["brand_name" => $brand, "logo" => $imgUrl]
            );
        }

        return response()->json("done");
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
