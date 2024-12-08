<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = DB::table('kategoris');
        if (isset($request['kategori']) && $request['kategori'] != "") {
            $data->where("kategori", "like", "%" . $request["kategori"] . "%");
        }
        $data = $data->whereNull('deleted_at')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'kategori' => [
                'required',
                Rule::unique('kategoris')->whereNull('deleted_at')
            ]
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 401);
        }

        $kategori = Kategori::create([
            'kategori' => $request['kategori']
        ]);

        if ($kategori) {
            return response()->json([
                'status' => true,
                'data' => $kategori
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Input Fail'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (empty($kategori)) {
            return response()->json([
                'status' => false,
                'message' => 'Data Not Found'
            ], 404);
        } else {
            return response()->json([
                'status' => true,
                'data' => $kategori
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'kategori' => [
                'required',
                Rule::unique('kategoris')->whereNull('deleted_at')->ignore($id)
            ]
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 401);
        }

        $kategori = Kategori::find($id);
        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Data Not Found'
            ], 404);
        }
        $kategori->kategori = $request['kategori'];
        $kategori->save();
        return response()->json([
            'status' => true,
            'data' => $kategori
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Kategori::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data Not Found'
            ], 404);
        }
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Success Delete'
        ]);
    }
}
