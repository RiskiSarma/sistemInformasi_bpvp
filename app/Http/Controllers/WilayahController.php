<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class WilayahController extends Controller
{
    public function cities(Request $request)
    {
        $cities = City::when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->orderBy('name')
            ->limit($request->search ? 50 : 600)
            ->get(['code as id', 'name']); // ← code as id

        return response()->json($cities);
    }

    public function districts(Request $request)
    {
        if (!$request->city_id) return response()->json([]);

        $districts = District::where('city_code', $request->city_id) // ← city_code
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->orderBy('name')
            ->get(['code as id', 'name']); // ← code as id

        return response()->json($districts);
    }

    public function villages(Request $request)
    {
        if (!$request->district_id) return response()->json([]);

        $villages = Village::where('district_code', $request->district_id) // ← district_code
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->orderBy('name')
            ->get(['code as id', 'name']); // ← code as id

        return response()->json($villages);
    }
}