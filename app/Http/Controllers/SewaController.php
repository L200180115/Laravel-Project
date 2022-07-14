<?php

namespace App\Http\Controllers;

use App\Models\DataMobil;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = DataMobil::all();
        return view('rental.index', [
            "title" => "Daftar Mobil"
        ])
            ->with(['datas' => $datas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $datamobil = DataMobil::find($id);
        // dd($datamobil);
        return view('rental.edit', [
            "title" => "Order Kendaraan",
            "data" => $datamobil
        ]);
    }

    public function post(Request $request)
    {
        // dd($request->driver);

        $tgl_pinjam = strtotime($request->tgl_pinjam);
        $tgl_kembali = strtotime($request->tgl_kembali);
        $hari = ($tgl_kembali - $tgl_pinjam) / 86400;

        if ($request->driver == "on") {
            $biaya = ($request->harga * $hari) + 100000;
        } else {
            $biaya = $request->harga * $hari;
        }

        // dd($biaya);
        return view('rental.post', [
            "title" => "Order Kendaraan",
            "data" => $request,
            "hari" => $hari,
            "biaya" => $biaya,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);

        $request->validate([
            'id_mobil' => 'required|exists:data_mobil,id',
            'id_user' => 'required|exists:users,id',
            'tgl_pinjam' => 'required',
            'tgl_kembali' => 'required',
            'biaya' => 'required'
        ]);

        Transaksi::create([
            'mobil_id' => $request->id_mobil,
            'user_id' => $request->id_user,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'harga' => $request->biaya,
        ]);

        return redirect()->route('rental');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $order = new Transaksi();
        return view('rental.edit', [
            "title" => "Order Kendaraan",
            "data" => DataMobil::find($id)
        ])->with([
            'order' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DataMobil::find($request->data_mobil->id);
        User::find($request->users->id);
        $dataTransaksi = [
            'invoice_no' => Transaksi::generateInvoice(date('Y-m-d')),
            'mobil_id' => $request->data_mobil->id,
            'user_id' => $request->users->id,
            'tgl_pinjam' => $request->transaksi->tgl_pinjam,
            'tgl_kembali' => $request->transaksi->tgl_kembali,
            'harga' => $request->data_mobil->harga,
            'jumlah' => Carbon::parse($request->transaksi->tgl_pinjam)->diffInDays($request->transaksi->tgl_kembali) * $request->data_mobil->harga,
            'status' => 'proses',
        ];

        Transaksi::where('id', $id)->create($dataTransaksi);
        DataMobil::find($id)->update(['status' => 'Terpakai']);

        return redirect('/rental');
    }

    public function complete(Request $request, $id)
    {
        $transaction = Transaksi::find($id);
        $transaction->update([
            'tgl_dikembalikan' => $request->tgl_dikembalikan,
            'status' => 'selesai',
            'denda' => Carbon::parse($transaction->tgl_kembali)->diffInDays($request->tgl_dikembalikan) * $transaction->data_mobil->denda,
            'jumlah' => Carbon::parse($transaction->tgl_kembali)->diffInDays($request->tgl_dikembalikan) * $transaction->data_mobil->denda + $transaction->jumlah

        ]);
        DataMobil::find($transaction->mobil_id)->update(['status' => 'tersedia']);
        return redirect('/rental')->with('success', 'Orderan Telah Selesai');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
