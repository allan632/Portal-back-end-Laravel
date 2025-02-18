<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\PalletEntryExit;
use App\Models\QtdPaleteDoc;
use App\Models\QtdPaleteFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\FtDocAuxiliar;
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

                "receivedDataDoc.PBR" => "required|numeric",
                "receivedDataDoc.CHEP" => "required|numeric",
                "receivedDataDoc.Descartavel" => "required|numeric",
            
                "receivedDataFis.PBR" => "required|numeric",
                "receivedDataFis.CHEP" => "required|numeric",
                "receivedDataFis.Descartavel" => "required|numeric",
            ]);

            $consulta = DB::transaction(function () use ($req,$validated) {
          
                
                $palletEntry = PalletEntryExit::create([
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
                    //'created_at' => now(), // Melhor usar o now() para a data atual
                ]);
                
                QtdPaleteDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'PBR',
                    'QtdPalete' => $validated['receivedDataDoc']['PBR']
                ]);
                
                QtdPaleteDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'CHEP',
                    'QtdPalete' => $validated['receivedDataDoc']['CHEP']
                ]);
                
                QtdPaleteDoc::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'Descartavel',
                    'QtdPalete' => $validated['receivedDataDoc']['Descartavel']
                ]);
                
                QtdPaleteFisica::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'PBR',
                    'QtdPalete' => $validated['receivedDataFis']['PBR']
                ]);
                
                QtdPaleteFisica::create([
                    'IdDoc' => $palletEntry->IdDoc,
                    'TpPalet' => 'CHEP',
                    'QtdPalete' => $validated['receivedDataFis']['CHEP']
                ]);
                
                QtdPaleteFisica::create([
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
            $imagem = FtDocAuxiliar::create([
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
    public function show(PalletEntryExit $pallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PalletEntryExit $pallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req, PalletEntryExit $pallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PalletEntryExit $pallet)
    {
        //
    }
}
