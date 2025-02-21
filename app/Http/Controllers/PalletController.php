<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\MovPalete;
use App\Models\MovPaleteQtdDoc;
use App\Models\MovPaleteQtdFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MovPaleteAnexoDoc;

use Carbon\Carbon;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function test(Request $req)
    {
        try {
            $validated = $req->validate([
                "formData.NrDocumento" => "required|string",
                "formData.NrSerie" => "required|string",
                "formData.TpDoc" => "required|string",
                "formData.TpOperacao" => "required|string",
                "formData.CNPJRemetente" => "required|string",
                "formData.CNPJDestinatario" => "required|string",
                "formData.FilialRecebedoura" => "required|string",
                "formData.DataRegistro" =>"required|date",
                "receivedDataDoc.PBR" => "required|numeric",
                "receivedDataDoc.CHEP" => "required|numeric",
                "receivedDataDoc.Descartavel" => "required|numeric",
            
                "receivedDataFis.PBR" => "required|numeric",
                "receivedDataFis.CHEP" => "required|numeric",
                "receivedDataFis.Descartavel" => "required|numeric",
            ]);
            
            return response()->json([
                "formData" => [
                    "NrDocumento" => $validated["formData"]["NrDocumento"],
                    "NrSerie" => $validated["formData"]["NrSerie"],
                    "TpDoc" => $validated["formData"]["TpDoc"],
                    "TpOperacao" => $validated["formData"]["TpOperacao"],
                    "CNPJRemetente" => $validated["formData"]["CNPJRemetente"],
                    "CNPJDestinatario" => $validated["formData"]["CNPJDestinatario"],
                    "FilialRecebedoura" => $validated["formData"]["FilialRecebedoura"],
                ],
                "receivedDataDoc" => [
                    "PBR" => $validated["receivedDataDoc"]["PBR"],
                    "CHEP" => $validated["receivedDataDoc"]["CHEP"],
                    "Descartavel" => $validated["receivedDataDoc"]["Descartavel"],
                ],
                "receivedDataFis" => [
                    "PBR" => $validated["receivedDataFis"]["PBR"],
                    "CHEP" => $validated["receivedDataFis"]["CHEP"],
                    "Descartavel" => $validated["receivedDataFis"]["Descartavel"],
                ],
            ]);
            
        } catch (\Throwable $e) {
            return  response()->json(["error"=> $e]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function registerEntryPallet(Request $req)
    {
        try{

            $validated = $req->validate([
                "formData.NrDocumento" => "required|string",
                "formData.NrSerie" => "required|string",
                "formData.TpDoc" => "required|string",
                "formData.TpOperacao" => "required|string",
                "formData.CNPJRemetente" => "required|string",
                "formData.CNPJDestinatario" => "required|string",
                "formData.FilialRecebedoura" => "required|string",
                "formData.DataRegistro" =>"required|date",
                "formData.TpProc"=> "required|string",

                "receivedDataDoc.PBR" => "required|numeric",
                "receivedDataDoc.CHEP" => "required|numeric",
                "receivedDataDoc.Descartavel" => "required|numeric",
            
                "receivedDataFis.PBR" => "required|numeric",
                "receivedDataFis.CHEP" => "required|numeric",
                "receivedDataFis.Descartavel" => "required|numeric",
            ]);

            $consulta = DB::transaction(function () use ($req,$validated) {
          
                
                $palletEntry = MovPalete::create([
                    'NrFun' => trim($req->user()->NrFun),
                    'NrDocumento' => trim($validated['formData']['NrDocumento']),
                    'NrSerie' => trim($validated['formData']['NrSerie']),
                    'TpDoc' => trim($validated['formData']['TpDoc']),
                    'TpOperacao' => trim($validated['formData']['TpOperacao']),
                    'CNPJRemetente' => trim($validated['formData']['CNPJRemetente']),
                    'CNPJDestinatario' => trim($validated['formData']['CNPJDestinatario']),
                    'FilialRecebedoura' => trim($validated['formData']['FilialRecebedoura']),
                    'DataEmissaoDoc'=> Carbon::parse(Carbon::now())->format('d-m-Y H:i'),
                    'DataRegistro'=>Carbon::parse($validated['formData']['DataRegistro'])->format('d-m-Y H:i'),
                    'TpProc'=>trim($validated['formData']['TpProc'])

                    //'created_at' => now(), // Melhor usar o now() para a data atual
                ]);
                
                MovPaleteQtdDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'PBR',
                    'QtdPalete' => $validated['receivedDataDoc']['PBR']
                ]);
                
                MovPaleteQtdDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'CHEP',
                    'QtdPalete' => $validated['receivedDataDoc']['CHEP']
                ]);
                
                MovPaleteQtdDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'Descartavel',
                    'QtdPalete' => $validated['receivedDataDoc']['Descartavel']
                ]);
                
                MovPaleteQtdFisica::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'PBR',
                    'QtdPalete' => $validated['receivedDataFis']['PBR']
                ]);
                
                MovPaleteQtdFisica::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'CHEP',
                    'QtdPalete' => $validated['receivedDataFis']['CHEP']
                ]);
                
                MovPaleteQtdFisica::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'Descartavel',
                    'QtdPalete' => $validated['receivedDataFis']['Descartavel']
                ]);
                return $palletEntry->IdDoc;
            });
 
        return response()->json([
            "Validacao"=> 'sucess',
            "IdDoc"=> $consulta
        ], 201);
        } catch( \Exception $e) {
            return response()->json(["erro"=>$e], 402);

        }
    }

    public function registerPhoto(Request $req){
        try{


            // Salva a imagem no diretório 'storage/app/public/imagens'
            $caminho = $req->file('FotoDoc')->store('imagens', 'public');

            // Salvando apenas o caminho no banco
            $imagem = MovPaleteAnexoDoc::create([
                "IdDoc"=>$req->IdDoc,
                'FotoDoc' => $caminho,
                'DataAtualizacao'=>"14-02-2025 15:30:00",
                'NrFun'=>$req->user()->NrFun

            ]);
            return response()->json([
                "Validacao"=> "sucesso"], 201);
        } catch(\Exception $e){
            return response()->json(["message"=>$e]);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req)
    {
        //
    }

    /**
     * Display the specified resource.
     */
   
}
