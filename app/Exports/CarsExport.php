<?php

namespace App\Exports;

use App\Models\Car;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CarsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Car::all();
    }

    public function headings(): array
    {
        return [
            "id",
            "handle",
            "title",
            "hero_image",
            "variant_price",
            "condition",
            "maileage",
            "gallery",
            "body_html",
            "created_at",
            "updated_at",
            "status",
        ];
    }
}
