<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Antrian;
use App\Poli;
use App\Operasi;
use App\Jadwalpoli;
use App\Referensi;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Rekapantrian extends Controller
{
    //
    public function rekapantrian(request $request)
    {
       //menangkap data dari input
       $tanggalperiksa= $request->tanggalperiksa;
        $kodepoli=$request->kodepoli;
        $polieksekutif=$request->polieksekutif;

        $validator = Validator::make($request->all(), [
            'tanggalperiksa' => 'required|string|max:255', 
        ]);

        //gettanggalperiksa
        $gettanggalperiksa=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
        ->where('kodepoli','=', $kodepoli)
        ->where('polieksekutif','=', $polieksekutif)
        ->first('tanggalperiksa');

        $getkodepoli=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
        ->where('kodepoli','=', $kodepoli)
        ->where('polieksekutif','=', $polieksekutif)
        ->first('kodepoli');

        

        if ($validator->fails()) {
            return response()->json([
                "response"=>([
                 ]), "metadata"=>([
                     "message"=>"Tanggal Periksa belum diisi",
                     "code"=>400
                 ])
            ]);
        }elseif(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tanggalperiksa)){
            return response()->json([
                "response"=>([
                 ]), "metadata"=>([
                     "message"=>"Format Tanggal salah",
                     "code"=>400
                 ])
            ]);
        }
        elseif ($gettanggalperiksa == null ){
            return response()->json([
                       "response"=>([
                        ]), "metadata"=>([
                            "message"=>"Tanggal Periksa / kode poli  tidak sesuai",
                            "code"=>400
                        ])
                   ]);
        }elseif( $getkodepoli == null ){
            return response()->json([
                "response"=>([
                 ]), "metadata"=>([
                     "message"=>"Kode Poli salah ",
                     "code"=>400
                 ])
            ]);
        
        }
        else{

            $max=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
            ->where('kodepoli','=', $kodepoli)
            ->where('polieksekutif','=', $polieksekutif)
            ->count('NOMOR');

            $terlayani=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
            ->where('kodepoli','=', $kodepoli)
            ->where('polieksekutif','=', $polieksekutif)
            ->where('statusperiksa','=', 0)
            ->count('statusperiksa');
     
            $poli = Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('namapoli')
            ->get();
             $namapoli=$poli[0]->namapoli;
     
             $update=carbon::now();
             $update2=strtotime($update)*1000;
         

            return response()->json([
                       "response"=>([
                           "namapoli"=>$namapoli,
                           "totalantrian"=>$max,
                           "jumlahterlayani"=>$terlayani,
                           "lastupdate"=>$update2,
                        ]), "metadata"=>([
                            "message"=>"ok",
                            "code"=>200
                        ])
                   ]);
        }

      
       

    }
}
