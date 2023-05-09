<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Mahasiswa_MataKuliah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use Dompdf\Dompdf;
class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $mahasiswas = Mahasiswa::all();
        if($request->has('search')){
            $mahasiswas = Mahasiswa::where('nim', 'LIKE', '%' . request('search') . '%')
                ->orWhere('nama', 'LIKE', '%' . request('search') . '%')
                ->orWhere('tanggal_lahir', 'LIKE', '%' . request('search') . '%')
                ->orWhere('jurusan', 'LIKE', '%' . request('search') . '%')
                ->orWhere('email', 'LIKE', '%' . request('search') . '%')
                ->orWhere('no_hp', 'LIKE', '%' . request('search') . '%')
                ->paginate(5);

            return view('mahasiswas.index', ['mahasiswas' => $mahasiswas]);
        }else{
            $mahasiswas = Mahasiswa::orderBy('nim', 'desc')->paginate(5);
            return view('mahasiswas.index', compact('mahasiswas'))->with('i', (request()->input('page', 1) - 1) * 5);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('mahasiswas.create',['kelas'=>$kelas]);
    }
    public function edit($nim)
    {
        $Mahasiswa = Mahasiswa::find($nim);
        $kelas = Kelas::all();
        return view('mahasiswas.edit', compact('Mahasiswa', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMahasiswaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //melakukan validasi data
        $request->validate([
            'nim' => 'required',
            'nama' => 'required',
            'tanggal_lahir' => 'required',
            'jurusan' => 'required',
            'email' => 'required',
            'no_hp' => 'required',
            'kelas' => 'required',
        ]);

        //fungsi eloquent untuk menambah data
        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim=$request->get('nim');
        $mahasiswa->nama=$request->get('nama');
        $mahasiswa->tanggal_lahir=$request->get('tanggal_lahir');
        $mahasiswa->jurusan=$request->get('jurusan');
        $mahasiswa->email=$request->get('email');
        $mahasiswa->no_hp=$request->get('no_hp');

        //fungsi eloquent untuk menambah data dengan relasi belongs to
        $kelas = new Kelas;
        $kelas->id = $request->get('kelas');

        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama

        return redirect()->route('mahasiswas.index')->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function show($nim)
    {
        $Mahasiswa = Mahasiswa::find($nim);
        return view('mahasiswas.detail', compact('Mahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     *
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMahasiswaRequest  $request
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nim)
    {
        //melakukan validasi data
        $request->validate([
            'nim' => 'required',
            'nama' => 'required',
            'tanggal_lahir' => 'required',
            'jurusan' => 'required',
            'email' => 'required',
            'no_hp' => 'required',
            'kelas' => 'required',
        ]);

        //fungsi eloquent untuk menambah data
        $mahasiswa = Mahasiswa::with('kelas')->where('nim',$nim)->first();
        $mahasiswa->nim=$request->get('nim');
        $mahasiswa->nama=$request->get('nama');
        $mahasiswa->tanggal_lahir=$request->get('tanggal_lahir');
        $mahasiswa->jurusan=$request->get('jurusan');
        $mahasiswa->email=$request->get('email');
        $mahasiswa->no_hp=$request->get('no_hp');

        //fungsi eloquent untuk menambah data dengan relasi belongs to
        $kelas = new Kelas;
        $kelas->id = $request->get('kelas');

        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama

        return redirect()->route('mahasiswas.index')->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function destroy($nim)
    {
        Mahasiswa::find($nim)->delete();

        return redirect()->route('mahasiswas.index')->with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function khs($nim)
    {
        $Mahasiswa = Mahasiswa::find($nim);

        return view('mahasiswas.khs', compact('Mahasiswa'));
    }
    public function cetak($nim) {
        $Mahasiswa = Mahasiswa::find($nim);
        $pdf = PDF::loadView('mahasiswas.cetak',['Mahasiswa'=> $Mahasiswa]);
        return $pdf->stream();
    }

}
