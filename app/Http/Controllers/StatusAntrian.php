<?php

namespace App\Http\Controllers;


use App\User;
use App\Antrian;
use App\Poli;
use App\Dokter;
use App\Operasi;
use App\Jadwalpoli;
use App\Referensi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use DateTime;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StatusAntrian extends Controller
{
    //
    public function statusantrian (request $request)
    {
        //menangkap data dari input
       $kodepoli=$request->kodepoli;
       $kodedokter= $request->kodedokter;
       $tanggalperiksa=$request->tanggalperiksa;
       $jampraktek=$request->jampraktek;

       // return $tanggal;    
       $tanggal = $request->tanggalperiksa;
       $day = date('D', strtotime($tanggal));

           $dayList = array(
               'Sun' => 'Minggu',
               'Mon' => 'Senin',
               'Tue' => 'Selasa',
               'Wed' => 'Rabu',
               'Thu' => 'Kamis',
               'Fri' => 'Jumat',
               'Sat' => 'Sabtu'
           );
           $hari= $dayList[$day];
      
       //ngambil nama poli
       $poli = Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('namapoli')
            ->get();
             $getnamapoli=$poli[0]->namapoli;
      
      //ngambil nama Dokter
       $dokter = Dokter::where('kodedokter', '=', $request->kodedokter)
            ->select('namadokter')
            ->get();
             $getnamadokter=$dokter[0]->namadokter;

      //get total antrean
      $max=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
            ->where('kodepoli','=', $kodepoli)
            ->count('NOMOR');
      
      //get antreanpanggil
      $sisantrian=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
            ->where('kodepoli','=', $kodepoli)
            ->where('statusperiksa','=','1')
            ->count('NOMOR');
       $sisantrian2=$max-$sisantrian;

       //get antreanpanggil
       $antreanpanggil3=Antrian::where('tanggalperiksa','=',$tanggalperiksa)
       ->where('kodepoli','=', $kodepoli)
       ->where('statusperiksa','=','1')
       ->max('NOMOR');
       
       //ngambilkodepanggilpoli
       $kodeantre=Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('kodeantri')
            ->get();
            $getkodeantre2=$kodeantre[0]->kodeantri;
        
        //get kuota
        $kuota = Jadwalpoli::where('kodepoli', '=', $request->kodepoli)
        ->where('namahari','=',$hari)
        ->select('kuota')
        ->get();
       $kuota2=$kuota[0]->kuota;;

       $sisakoutoajkn=$kuota2 - $antreanpanggil3 ;

           return response()->json([
                      "response"=>([
                          "namapoli"=>$getnamapoli,
                          "namadokter"=>$getnamadokter,
                          "totalantrian"=>$max,
                          "sisaantrean"=> $sisantrian2,
                          "antreanpanggil"=>$getkodeantre2.$antreanpanggil3,
                           "sisakuotajkn"=>$sisakoutoajkn,
                            "kuotajkn"=>$kuota2,
                           "sisakuotanonjkn"=>$sisakoutoajkn,
                          "kuotanonjkn"=>$kuota2,
                         "keterangan"=>"",
                       ]), "metadata"=>([
                           "message"=>"ok",
                           "code"=>200
                       ])
                  ]);
    //    }

          
    }

    //sisa antrian 
    public function sisaantrian (request $request)
    {
        
        //menangkap inputan 
        $kodebooking=$request->kodebooking;

        //get no antrian
        $noantri=Antrian::where('kodebooking','=',$kodebooking)
        ->select('nomor')
        ->get();
        $noantri2=$noantri[0]->nomor;

        //get kodeantri
        $kodeantri=Antrian::where('kodebooking','=',$kodebooking)
        ->select('kodeantri')
        ->get();
        $kodeantri2=$kodeantri[0]->kodeantri;

         //get namapoli
         $namapoli=Antrian::where('kodebooking','=',$kodebooking)
         ->select('namapoli')
         ->get();
         $namapoli2=$namapoli[0]->namapoli;
         
         //get namadokter
         $namadokter=Antrian::where('kodebooking','=',$kodebooking)
         ->select('namadokter')
         ->get();
         $namadokter2=$namadokter[0]->namadokter;

         //getkodepoli
         $kodepoli=Antrian::where('kodebooking','=',$kodebooking)
         ->select('kodepoli')
         ->get();
         $kodepoli2=$kodepoli[0]->kodepoli;
         
         //gettanggalperiksa
         $tglperiksa=Antrian::where('kodebooking','=',$kodebooking)
         ->select('tanggalperiksa')
         ->get();
         $tglperiksa2=$tglperiksa[0]->tanggalperiksa;
        
        //get no kode antri
         // return $tanggal;    
       $day = date('D', strtotime($tglperiksa2));

           $dayList = array(
               'Sun' => 'Minggu',
               'Mon' => 'Senin',
               'Tue' => 'Selasa',
               'Wed' => 'Rabu',
               'Thu' => 'Kamis',
               'Fri' => 'Jumat',
               'Sat' => 'Sabtu'
           );
           $hari= $dayList[$day];
        
        //get jumlah keseluruhan antrian
         //get total antrean
      $max=Antrian::where('tanggalperiksa','=',$tglperiksa2)
      ->where('kodepoli','=', $kodepoli2)
      ->count('NOMOR');

      //sisaantrian
      $sisaantri=$max - $noantri2;
      
      //getwaktuperiksa
      $waktuperiksa=Antrian::where('kodebooking','=',$kodebooking)
      ->select('waktuperiksa')
      ->get();
      $waktuperiksa2=$waktuperiksa[0]->waktuperiksa;

      $dt1 = Carbon::now();
      $waktu=strtotime($dt1);

      
      //sisawaktutunggu
       
      $checkTime = strtotime('09:00:59');
      $time1= date('H:i:s', $checkTime);
      
      
      $loginTime = strtotime('09:01:00');
      $diff = $waktu - $loginTime;
      
    
       //get antreanpanggil
       $antreanpanggil3=Antrian::where('tanggalperiksa','=',$tglperiksa2)
       ->where('kodepoli','=', $kodepoli2)
       ->where('statusperiksa','=','1')
       ->max('NOMOR');

        return response()->json([
            "response"=>([
                "nomorantrean"=>$kodeantri2.$noantri2,
                 "namapoli"=>$namapoli2,
                 "namadokter"=>$namadokter2,
                 "sisaantrean"=> $sisaantri,
                 "antreanpanggil"=>$kodeantri2."-".$antreanpanggil3,
                // "test"=>$waktu,
                 "waktutunggu"=>$diff,
                 "keterangan"=>"",
             ]), "metadata"=>([
                 "message"=>"ok",
                 "code"=>200
             ])
        ]);

          
    }

    //batal antrian 
    public function batalantrian(request $request)
    {
        DB::table('no_antrian_ruangan')->where('kodebooking',$request->kodebooking)->update([
            'checkin' => 2,
            'keterangan'=>$request->keterangan,
        ]);
        return response()->json([
            "response"=>([
                "metadata"=>([
                    "message"=>"ok",
                    "code"=>200
                ])
            ])
                ]);

    }

    //checkin
    public function checkin(request $request)
    {
        DB::table('no_antrian_ruangan')
        ->where('kodebooking',$request->kodebooking)
        ->where('waktuperiksa',$request->waktu)
        ->update([
            'checkin' => 1,
        ]);

     

        return response()->json([
            "response"=>([
                "metadata"=>([
                    "message"=>"ok",
                    "code"=>200
                ])
            ])
                ]);

    }
}
