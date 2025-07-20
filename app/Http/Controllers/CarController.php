<?php

namespace App\Http\Controllers;

use App\Exports\CarsExport;
use App\Imports\CarsImport;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        // $condition = $request->query("condition");
        $cars = Car::where("status", "!=", 0)
            ->orderBy('condition', 'desc')
        // ->orderBy("id", "desc")
            ->orderBy('variant_price', 'desc')
            ->paginate(12);
        return response()->json($cars);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cars = Car::where("handle", "=", $id)->firstOrFail();
        return response()->json($cars);
    }

    public function search($cat)
    {
        $brand = Brand::where("id", "=", $cat)->firstOrFail();

        $cars = Car::where("status", "!=", 0)
            ->where("brand", "like", $brand->brand_name)
            ->orderBy('condition', 'desc')
            ->orderBy('variant_price', 'desc')
            ->paginate(12);
        return response()->json($cars);
    }

    public function advanceSearch(Request $request)
    {

        $cars = Car::where("title", "like", "%" . $request->name . "%" );
        return response()->json( $cars->get() );

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

    public function import(Request $request)
    {
        $file = $request->file('csv_file');
        $data = Excel::toArray(new CarsImport, $file);
        $allImages = Storage::disk('public')->allFiles('cars');
        $imgArr = [];
        $cars = [];

        foreach ($data[0] as $car) {
            if (isset($car['title'])) {
                array_push($cars, $car);
            }
        }

        foreach ($allImages as $img) {
            if (str_contains($img, '.JPG') || str_contains($img, '.jpg') || str_contains($img, ".jpeg")) {
                $fileArr = explode("/", $img);

                $title = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fileArr[count($fileArr) - 2])));

                $key = array_search($title, array_column($cars, 'handle'));

                array_push($imgArr, $title);

                if ($key !== false && empty($cars[$key]["gallery"])) {
                    $cars[$key]["gallery"] = [];
                }

                if ($key !== false) {
                    array_push($cars[$key]["gallery"], $img);
                }
            }
        }

        // dd($car);

        foreach ($cars as $car) {
            if (array_key_exists("gallery", $car)) {
                $car["gallery"] = json_encode($car["gallery"]);
            }

            if ($car['status'] === "active" || $car['status'] === 1) {
                $car['status'] = 1;
            } else {
                $car['status'] = 0;
            }

            $brand = Brand::where("brand_name", "=", $car["brand"])->get();

            if (isset($brand[0])) {
                $car["brand"] = $brand[0]["id"];
            }

            Car::updateOrCreate(
                ["handle" => $car['handle']],
                $car
            );
        }

        return back()->with('success', 'All good!');
    }

    public function export()
    {
        return Excel::download(new CarsExport, 'cars.xlsx');
    }
}
