<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\MovPalete;
use App\Models\MovPaleteQtdDoc;
use App\Models\MovPaleteQtdFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MovPaleteAnexoDoc;
use App\Models\MovPaleteSaldo;
use App\Models\MovVinculoNfSaidaEntrada;
use Carbon\Carbon;
use Faker\Core\Number;
use Illuminate\Auth\Events\Validated;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function consultRemetentEntryPallet(Request $req)
    {
        try{

        $validated = $req->validate([
            "CNPJRemetente"=> "required|string",
        ]);

// Pegando os IdDoc conforme os critérios especificados
$result = DB::table('MovPalete as mp')
    ->join('MovPaleteSaldo as b', 'mp.IdDoc', '=', 'b.IdDocEntradaFisica')
    ->select(
        'mp.IdDoc',
        'mp.NrDocumento',
        'mp.NrSerie',
        'mp.dataRegistro',
        'b.QtdPalete',
        'b.TpPalet'
    )
    ->where('mp.CNPJRemetente', $validated['CNPJRemetente'])
    ->where('mp.TpProc', 'Entrada')
    ->orderBy('mp.dataRegistro', 'asc')
    ->get();
        //
        //  CHEP  =  = 0 
        //
        $items = [
            "IdDoc"=>$result[0]->IdDoc,
            "NrDocumento"=>
            $result[0]->TpPalet =>$result[0]->QtdPalete
        ];
        
    return response()->json(['message'=> $items ],200);
} catch(\Exception $e){
    return response()->json(['error'=>$e ],422);

}

    }

    // Formulario de Entrada
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
                "formData.TpProc"=> "required|string",
                'receivedDataFis'=> 'nullable|array',
                'receivedDataFis.PBR*'=> "nullable|numeric",
                'receivedDataFis.CHEP*'=> "nullable|numeric",
                'receivedDataFis.Descartavel*'=> "nullable|numeric"
            ]);
            dump($validated['receivedDataFis']);


                dump($validated['receivedDataFis']);
                dump($validated['receivedDataDoc']);


                
            // return response()->json([
            //     "formData" => [
            //         "NrDocumento" => $validated["formData"]["NrDocumento"],
            //         "NrSerie" => $validated["formData"]["NrSerie"],
            //         "TpDoc" => $validated["formData"]["TpDoc"],
            //         "TpOperacao" => $validated["formData"]["TpOperacao"],
            //         "CNPJRemetente" => $validated["formData"]["CNPJRemetente"],
            //         "CNPJDestinatario" => $validated["formData"]["CNPJDestinatario"],
            //         "FilialRecebedoura" => $validated["formData"]["FilialRecebedoura"],
            //     ],
            //     "receivedDataDoc" => [
            //         "PBR" => $validated["receivedDataDoc"]["PBR"],
            //         "CHEP" => $validated["receivedDataDoc"]["CHEP"],
            //         "Descartavel" => $validated["receivedDataDoc"]["Descartavel"],
            //     ],
            //     "receivedDataFis" => [
            //         "PBR" => $validated["receivedDataFis"]["PBR"],
            //         "CHEP" => $validated["receivedDataFis"]["CHEP"],
            //         "Descartavel" => $validated["receivedDataFis"]["Descartavel"],
            //     ],
            // ]);
            //Validar se o NRDocumento Existe retorna


            $existe = MovPalete::where('TpProc', $validated["formData"]["TpProc"])
            ->where('NrSerie', $validated["formData"]["NrSerie"])
            ->where('NrDocumento', $validated["formData"]["NrDocumento"])
            ->count() > 1; // Verifica se há mais de uma ocorrência
            // if($existe){
            //     return  response()->json(["message"=>  "Essa entrada ja foi cadastrada"]);
            // }


            return  response()->json(["message"=>  $validated['receivedDataDoc']]);
            
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
            //Valida tipo e se e requirido
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
            
                'receivedDataFis'=> 'nullable|array',
                'receivedDataFis.PBR*'=> "nullable|numeric",
                'receivedDataFis.CHEP*'=> "nullable|numeric",
                'receivedDataFis.Descartavel*'=> "nullable|numeric"
            ]);



            //valida logica de diferença de palete no documento para o fisico 
            $calculoSobra = [];
            $calculoFalta = [];
            foreach ($validated['receivedDataDoc'] as $tipo => $qtd){
            //se o receivedDataFis = null ele vai ser igual ao doc
                if($validated['receivedDataFis'] === null){
                    $validated['receivedDataFis'] = $validated['receivedDataDoc'];
                    break;
                };
            //se o receivedDataFis < receivedDataDoc ele manda receivedDataFis para o saldo, calcula a diferença e o que sobra no receivedDataDoc vai para divergencia
                if($validated['receivedDataFis']["$tipo"] < $validated['receivedDataDoc']["$tipo"] ){
                    $calculoFalta["$tipo"] = intval($qtd) - intval($validated['receivedDataFis']["$tipo"]);
                    continue;
                }
            //se o receivedDataFis > receivedDataDoc calcula a diferença e o que sobra no fisico manda para divergencia
                if($validated['receivedDataFis']["$tipo"] > $validated['receivedDataDoc']["$tipo"] ){
                    $calculoSobra["$tipo"] = intval($validated['receivedDataFis']["$tipo"]) - intval($qtd);
                    $validated['receivedDataFis']["$tipo"] -= $calculoSobra["$tipo"];
                    continue;
                }

            }
            
            //se o receivedDataFis = receivedDataDoc ele pega os dados do doc
            $consulta = DB::transaction(function () use ($req,$validated) {
                      
                $existe = MovPalete::where('TpProc', $validated["formData"]["TpProc"])
                ->where('NrSerie', $validated["formData"]["NrSerie"])
                ->where('NrDocumento', $validated["formData"]["NrDocumento"])
                ->count() > 0; // Considera duplicado apenas se houver mais de um
        
                if ($existe) {
                    return response()->json([
                        "message" => "Essa entrada já foi cadastrada."
                    ], 409);
                }

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
                
        // Inserção dinâmica usando os próprios arrays validados
        foreach ($validated['receivedDataDoc'] as $tipo => $qtd) {
            MovPaleteQtdDoc::create([
                'IdDoc' => $palletEntry->IdDoc,
                'TpPalet' => $tipo,
                'QtdPalete' => $qtd
            ]);
        


        }
    
        foreach ($validated['receivedDataFis'] as $tipo => $qtd) {
            MovPaleteQtdFisica::create([
                'IdDoc' => $palletEntry->IdDoc,
                'TpPalet' => $tipo,
                'QtdPalete' => $qtd
            ]);
            MovPaleteSaldo::create([
                'IdDocEntradaFisica' => $palletEntry->IdDoc,
                'TpPalet' => $tipo,
                'QtdPalete' => $qtd
            ]);

        }

        return $palletEntry->IdDoc;
    });
    return response()->json([
        "message" => "Documento cadastrado com sucesso.",
        "IdDoc" => $consulta
    ], 201);
        } catch( \Exception $e) {
            return response()->json(["erro"=>$e], 402);

        }
    }


    public function registerPhotoDocAuxiliarEntry(Request $req){
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

     // { Registrar Saida}
    public function registerExitPallet(Request $req)
    {
        try{
            //Valida tipo e se e requirido
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
            
                'receivedDataFis'=> 'required|array',
                'receivedDataFis.PBR*'=> "required|numeric",
                'receivedDataFis.CHEP*'=> "required|numeric",
                'receivedDataFis.Descartavel*'=> "required|numeric"
            ]);
            
        } catch( \Exception $e){

        }
    }

    /**
     * Display the specified resource.
     */
   
     // Formulario de Saida
}
