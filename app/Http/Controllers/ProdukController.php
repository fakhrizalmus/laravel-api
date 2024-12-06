<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request['limit'];
        $offset = 0;
        if (isset($request['offset']) && $request['offset'] != '') {
            $offset = $request['offset'];
        }
        $query = Produk::query();
        if (isset($limit)) {
            $query->skip($offset)->take($limit);
        }
        if (isset($request['namaproduk']) && $request['namaproduk'] != "") {
            $query->where("nama_produk", "like", "%" . $request["namaproduk"] . "%");
        }
        if (isset($request['detail']) && $request['detail'] != "") {
            $query->where("detail", "like", "%" . $request["detail"] . "%");
        }
        $produk = $query->get();
        return response()->json([
            'status' => true,
            'data' => $produk
        ]);
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
            'nama_produk' => 'required|unique:produks',
            'detail' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 400);
        }

        $produk = Produk::create([
            'nama_produk' => $request['nama_produk'],
            'detail' => $request['detail']
        ]);

        if ($produk) {
            return response()->json([
                'status' => true,
                'data' => $produk
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
        $data = Produk::find($id);
        if (empty($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Data Not Found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nama_produk' => 'required|unique:produks',
            'detail' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 400);
        }

        $data = Produk::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data Not Found'
            ], 404);
        }
        $data->nama_produk = $request['nama_produk'];
        $data->detail = $request['detail'];
        $data->save();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Produk::find($id);
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
