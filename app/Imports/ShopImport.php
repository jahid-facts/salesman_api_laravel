<?php

namespace App\Imports;

use App\Models\Shop;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShopImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Shop([
            'name'      => $row['name'],
            'shop_code' => $row['shop_code'],
            'address'   => $row['address'],
            'number'    => $row['number'],
            'latitude'  => $row['latitude'],
            'longitude' => $row['longitude'],
            'status'    => $row['status'],
        ]);
    }
}
