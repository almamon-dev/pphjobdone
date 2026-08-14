<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;

class CaseStudyApiController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseStudy::where('is_active', true)->latest();

        if ($request->has('category') && !empty($request->category) && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('limit')) {
            $limit = (int) $request->limit;
            $caseStudies = $query->take($limit)->get();
        } else {
            $caseStudies = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $caseStudies,
        ]);
    }

    public function show($id)
    {
        $caseStudy = CaseStudy::where('is_active', true)->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $caseStudy,
        ]);
    }
}
