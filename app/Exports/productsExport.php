<?php

namespace App\Exports;

use App\Category;
use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class productsExport implements WithHeadings, FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $productsData = Product::select('category_id', 'product_name', 'product_code', 'product_color', 'price')
            ->where('status', 1)->orderBy('id', 'Desc')->get();
        //get category name from category id
        foreach($productsData as $key=>$product)
        {
            $catName = Category::select('name')->where('id', $product->category_id)->first();
            $productsData[$key]->category_id = $catName->name;
        }
        return $productsData;
    }

    //export with headings
    public function headings(): array
    {
        return['Category Name', 'Product Name', 'ProductCode', 'Product Color', 'Price'];
    }
}
