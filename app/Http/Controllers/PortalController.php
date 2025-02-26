<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PortalController extends Controller {
    public function searchFilias(){
        try{

            
            $filias = DB::connection('Portal')->select('select CDEMPRESA
        ,DSEMPRESA
from softran_renovacao.dbo.SISEMPRE');     

            
            return response()->json($filias);

        } catch (\Exception $error){
            return response()->json(["Error"=>$error]);
        }
    }
    public function searchDestinatariosRemetentes(){
        try{

            
            $destinatariosRemetentes = DB::connection('Portal')->select('select CdInscricao
        ,DsEntidade
from softran_renovacao.dbo.SISCli
where DsEntidade is not null
and InAtivo = 0');

            return response()->json($destinatariosRemetentes);

        } catch (\Exception $error){
            return response()->json(["Error"=>$error]);
        }
    }
}