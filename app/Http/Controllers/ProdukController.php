<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $query = DB::table('produks as pr')->select(
            'us.name',
            'pr.nama_produk',
            'pr.detail',
            'kt.kategori',
            'pr.id as produk_id'
        );
        if (isset($limit)) {
            $query = $query->skip($offset)->take($limit);
        }
        if (isset($request['namaproduk']) && $request['namaproduk'] != "") {
            $query = $query->where("pr.nama_produk", "like", "%" . $request["namaproduk"] . "%");
        }
        if (isset($request['detail']) && $request['detail'] != "") {
            $query = $query->where("pr.detail", "like", "%" . $request["detail"] . "%");
        }
        if (isset($request['kategori']) && $request['kategori'] != "") {
            $query = $query->where("kt.id", "=", $request["kategori"]);
        }
        $produk = $query->whereNull('pr.deleted_at')->leftjoin('users as us', 'us.id', '=', 'pr.user_id')
            ->leftjoin('kategoris as kt', 'kt.id', '=', 'pr.kategori_id')->get();
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
            'nama_produk' => [
                'required',
                Rule::unique('produks')->whereNull('deleted_at')
            ],
            'detail' => 'required',
            'kategori_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 400);
        }

        $kategori = Kategori::find($request['kategori_id']);
        if (empty($kategori)) {
            return response()->json([
                'status' => false,
                'message' => 'Kategori Not Found'
            ], 400);
        }

        $produk = Produk::create([
            'nama_produk' => $request['nama_produk'],
            'detail' => $request['detail'],
            'kategori_id' => $request['kategori_id'],
            'user_id' => auth()->user()->id
        ]);

        if ($produk) {
            return response()->json([
                'status' => true,
                'data' => $produk
            ], 201);
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
        $data = DB::table('produks as pr')->leftJoin('users as us', 'us.id', 'pr.user_id')
            ->leftJoin('kategoris as kt', 'kt.id', '=', 'pr.kategori_id')
            ->select('pr.nama_produk', 'us.name', 'pr.detail', 'kt.kategori')
            ->whereNull('pr.deleted_at')
            ->where('pr.id', $id)->get();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $myproduk = DB::table('produks')->where('user_id', auth()->user()->id)->where('id', $id)->get();
        if (count($myproduk) == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Produk Not Found'
            ], 404);
        }
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
        $data->kategori_id = $request['kategori_id'];
        $data->user_id = auth()->user()->id;
        $data->save();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $myproduk = DB::table('produks')->where('user_id', auth()->user()->id)->where('id', $id)->get();
        if (count($myproduk) == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Produk Not Found'
            ], 404);
        }
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
