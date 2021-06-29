<?php

namespace App\Http\Controllers;

use App\User;
use App\Antrian;
use App\Poli;
use App\Operasi;
use App\Jadwalpoli;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;



class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        //return response()->json(compact('token'));
        $token=compact('token');

        return response()->json([
             "response"=>$token,
             "metadata"=>([
                "message"=>"ok",
                "code"=>200
             ])
        ]);
    }
	
	public function apiget()
    {
        return "apiget  api test";
    }
	
	

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }


//get no antrian bpjs old
//     public function Antrianbpjs1(Request $request)
//     {
     
  
//     $input=new Antrian;

//     $input->kodepoli=$request->kodepoli;
//     $input->tanggalperiksa=$request->tanggalperiksa;
//     $input->jenisrequest=$request->jenisrequest;
//     $input->nomorkartu=$request->nomorkartu;
//     $input->nik=$request->nik;
//     $input->notelp=$request->notelp;
//     $input->nomorreferensi=$request->nomorreferensi;
//     $input->jenisreferensi=$request->jenisreferensi;
//     $input->polieksekutif=$request->jenisreferensi;
//     $input->kodebooking=Str::random(20);
   
//     $timestamps=strtotime($request->tanggalperiksa)*1000;
//     $jumlah= 2+5;

//     //$tanggalcari=Antrian::where('tanggalperiksa',$request->tanggalperiksa)->first();
//      $max=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)->max('waktuperiksa');
//      $jml = $max+1;
    
//       $tanggalcari=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)->first();
//     //  return $tanggalcari;

//     if ($tanggalcari){
      
//       $input->waktuperiksa= $max + 300000;
//     }else{
//       $input->waktuperiksa= $timestamps;
//     }

//       //input kode antrian
//     $results = Poli::where('kodepoli', '=', $request->kodepoli)
//     ->select('kodeantri')
//     ->get();
//     $input->kodeantri=$results[0]->kodeantri;
    
//     //input nama poli
//   $results = Poli::where('kodepoli', '=', $request->kodepoli)
//   ->select('namapoli')
//   ->get();
//   $input->namapoli=$results[0]->namapoli ;

//     $input->save();

//         return response()->json([
//         "response"=>([
//             'nomorantrian'=>$input->kodeantri.$input->id,
//           'kodebooking'=>$input->kodebooking,
//           'jenisantrian'=>$input->jenisrequest,
//           'estimasidilayani'=>$input->waktuperiksa,
//           'namapoli'=>$input->namapoli,
//           'namadokter'=>null,
//         ]), "metadata"=>([
//             "message"=>"ok",
//             "code"=>200
//         ])
//     ]);


//     }

    public function rekapantrian(request $request)
    {
       //menangkap data dari input
       $tanggalperiksa= $request->tanggalperiksa;
        $kodepoli=$request->kodepoli;
        $polieksekutif=$request->polieksekutif;

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

    public function Listjadwaoperasi(request $request)
    {
         
        $tanggalawal=$request->tanggalawal;
        $tanggalakhir=$request->tanggalakhir;
       // $poli=$request->kodepoli;
        
        $data=Operasi::whereBetween('tanggaloperasi', [$tanggalawal, $tanggalakhir])->get();
     //    $poli = Poli::where('kodepoli', '=', $request->kodepoli)
     //    ->select('kodepoli')
     //    ->first();
 
 // $tanggal= $tanggalawal > $tanggalakhir;
 
 // return response()->json([
 //     "message"=> $tanggal
 // ]);
 
 
    if ( !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tanggalawal) ) {
        return response()->json([
            "response"=>([
                
                "metadata"=>([
                    "message"=>"format tanggal salah",
                    "code"=>200
                ])
            ])
        ]);
    }elseif (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tanggalakhir) ){
        
        return response()->json([
            "response"=>([
                
                "metadata"=>([
                    "message"=>"format tanggal salah",
                    "code"=>200
                ])
            ])
        ]);
    } elseif($tanggalawal > $tanggalakhir){
             return response()->json([
                 "response"=>([
                     
                     "metadata"=>([
                         "message"=>"tanggal akhir harus lebih besar dari tanggal awal",
                         "code"=>200
                     ])
                 ])
             ]);
    }else{
        return response()->json([
            "response"=>([
                "list"=>$data,
                "metadata"=>([
                    "message"=>"ok",
                    "code"=>200
                ])
            ])
        ]);
        
    }

   

    }

    public function Kodebookingoperasi(request $request)
    {
        $validator = Validator::make($request->all(), [
            'nopeserta' => 'required|string|max:13|min:13', 
        ]);

        $nopeserta=$request->nopeserta;

        $data=Operasi::where('nopeserta',$nopeserta)
        ->where('terlaksana', '0')
        ->select('kodebooking', 'tanggaloperasi','jenistindakan','kodepoli','namapoli', 'terlaksana')
        ->get();
        
            if ($validator->fails()){
                return response()->json([
                    "response"=>([
                        "metadata"=>([
                            "message"=>"no kartu tidak valid",
                            "code"=>200
                        ])
                    ])
             ]);

            }else {

                return response()->json([
                       "response"=>([
                           "list"=>$data,
                           "metadata"=>([
                               "message"=>"ok",
                               "code"=>200
                           ])
                       ])
                ]);
            }

    }

    //get no antrian bpjs new
    public function Antrianbpjs(request $request)
    
    {
        $tanggal = $request->tanggalperiksa;

        // return $tanggal;    
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
           
            
            
            
            //get namahari
            $gethari=Jadwalpoli::where('kodepoli',$request->kodepoli)
            ->wherenamahari($hari)
            ->wherestatus(1)
            ->first();

            //get max antrian 
            $max1=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)
            ->where('kodepoli', $request->kodepoli)
            ->max('NOMOR');

        

        

           if ($gethari AND $max1<=$gethari->kuota ){
               
            $input=new Antrian;

            $input->kodepoli=$request->kodepoli;
            $input->tanggalperiksa=$request->tanggalperiksa;
            $input->jenisrequest=$request->jenisrequest;
            $input->nomorkartu=$request->nomorkartu;
            $input->nik=$request->nik;
            $input->notelp=$request->notelp;
            $input->nomorreferensi=$request->nomorreferensi;
            $input->jenisreferensi=$request->jenisreferensi;
            $input->polieksekutif=$request->jenisreferensi;
            $input->kodebooking=Str::random(20);
           
            $timestamps=strtotime($request->tanggalperiksa)*1000;
            $jumlah= 2+5;
        
            //$tanggalcari=Antrian::where('tanggalperiksa',$request->tanggalperiksa)->first();
             $max=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)->max('waktuperiksa');
             $jml = $max+1;
            
              $tanggalcari=Antrian::where('tanggalperiksa','=',$request->tanggalperiksa)->first();
            //  return $tanggalcari;
        
            if ($tanggalcari){
              
              $input->waktuperiksa= $max + 300000;
            }else{
              $input->waktuperiksa= $timestamps;
            }
        
              //input kode antrian
            $results = Poli::where('kodepoli', '=', $request->kodepoli)
            ->select('kodeantri')
            ->get();
            $input->kodeantri=$results[0]->kodeantri;
            
            //input nama poli
          $results = Poli::where('kodepoli', '=', $request->kodepoli)
          ->select('namapoli')
          ->get();
          $input->namapoli=$results[0]->namapoli ;
        
            $input->save();
        
                return response()->json([
                "response"=>([
                    'nomorantrian'=>$input->kodeantri.$input->id,
                  'kodebooking'=>$input->kodebooking,
                  'jenisantrian'=>$input->jenisrequest,
                  'estimasidilayani'=>$input->waktuperiksa,
                  'namapoli'=>$input->namapoli,
                  'namadokter'=>null,
                ]), "metadata"=>([
                    "message"=>"ok",
                    "code"=>200
                ])
            ]);

           }else{

               return response()->json([
                "response"=>([
                    'message'=>'tidak ok',
                 
                ]), "metadata"=>([
                    "message"=>"tidak ok",
                    "code"=>400
                ])
            ],400);
           }
            
            

            


           


           

           

           

           

            

    }



    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
}