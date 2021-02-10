<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Houses;

class ApiController extends Controller
{
    public function filters(Request $request)
    {
        return response()->json([
            'success' => true,
            'bedrooms' => Houses::selectRaw('DISTINCT bedrooms')->get()->pluck('bedrooms'),
            'bathrooms' => Houses::selectRaw('DISTINCT bathrooms')->get()->pluck('bathrooms'),
            'storeys' => Houses::selectRaw('DISTINCT storeys')->get()->pluck('storeys'),
            'garages' => Houses::selectRaw('DISTINCT garages')->get()->pluck('garages'),
        ], 200);
    }

    public function search(Request $request)
    {
        $housesBuilder = Houses::query();

        if (!empty($request->search)) {
            $housesBuilder->where('name', 'like',  '%' .$request->search . '%');
        }

        if (!empty($request->bedrooms)) {
            $housesBuilder->whereIn('bedrooms', explode(',', $request->bedrooms));
        }

        if (!empty($request->bathrooms)) {
            $housesBuilder->whereIn('bathrooms', explode(',', $request->bathrooms));
        }

        if (!empty($request->storeys)) {
            $housesBuilder->whereIn('storeys', explode(',', $request->storeys));
        }

        if (!empty($request->garages)) {
            $housesBuilder->whereIn('garages', explode(',', $request->garages));
        }

        if (!empty($request->priceMin)) {
            $housesBuilder->where('price', '>=', $request->priceMin);
        }

        if (!empty($request->priceMax)) {
            $housesBuilder->where('price', '<=', $request->priceMax);
        }

        return response()->json(['success' => true, 'foundItems' => $housesBuilder->get()], 200);
    }
}
