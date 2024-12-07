<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->user()->id);
        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    public function myproduk()
    {
        $produk = DB::table('produks as pr')
            ->join('users as us', 'us.id', '=', 'pr.user_id')
            ->join('kategoris as kt', 'kt.id', '=', 'pr.kategori_id')
            ->select('pr.nama_produk', 'us.name', 'pr.detail', 'kt.kategori', 'pr.id as produk_id')
            ->where('pr.user_id', auth()->user()->id)
            ->whereNull('pr.deleted_at')
            ->get();
        return response()->json([
            'status' => true,
            'data' => $produk
        ], 200);
    }
}
