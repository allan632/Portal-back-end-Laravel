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
use App\Models\MovPaleteSaldoFilial;
use Illuminate\Database\QueryException;
use App\Models\MovPaletereSaldo;
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
            "CNPJDestinatario"=> "required|string",
        ]);

        // Pegando os IdDoc conforme os critérios especificados
        $result = DB::table('MovPalete as mp')
        ->join('MovPaleteSaldo as b', 'mp.IdDoc', '=', 'b.IdDocEntradaFisica')
        ->select([
            'mp.IdDoc',
            'mp.NrDocumento',
            'mp.NrSerie',
            'mp.CNPJDestinatario',
            'mp.dataRegistro',
            'b.QtdPalete',
            'b.TpPalet'
        ])
        ->where('mp.CNPJDestinatario', $validated['CNPJDestinatario'])
        ->where('mp.TpProc', 'Entrada')
        ->whereIn('b.TpPalet', ['CHEP', 'Descartavel', 'PBR'])
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('MovPaleteSaldo')
                ->whereRaw('MovPaleteSaldo.IdDocEntradaFisica = mp.IdDoc')
                ->whereIn('TpPalet', ['CHEP', 'Descartavel', 'PBR'])
                ->where('QtdPalete', '>', 0);
        })
        ->get();

        //  CHEP  =  = 0 
        //
        $varip = [];
        for ($i = 0; $i < \count($result) ; $i +=3) {
            $items = [
                "IdDoc"=>$result[$i]->IdDoc,
                "NrDocumento"=>$result[$i]->NrDocumento,
                "NrSerie"=>$result[$i]->NrSerie,
                "dataRegistro"=>$result[0]->dataRegistro,
                $result[$i]->TpPalet =>$result[$i]->QtdPalete,
                $result[$i+1]->TpPalet =>$result[$i+1]->QtdPalete,
                $result[$i+2]->TpPalet =>$result[$i+2]->QtdPalete,
            ];
            array_push($varip, $items);
        };

        
    return response()->json(['message'=> $varip ],200);
} catch(\Exception $e){
    return response()->json(['error'=>$e ],422);

}

    }
    /**
     * Display a listing of the resource.
     */
    public function CheckStorePalletFilial(Request $req)
    {
        try{

        $validated = $req->validate([
            "Remetente"=> "required|string",
        ]);

        // Pegando os IdDoc conforme os critérios especificados
        $result = DB::table('MovPaleteSaldoFilial')
        ->select(['DsFilial','PBR', 'CHEP', 'Descartavel'])
        ->where('Cnpj', $validated['Remetente'])
        ->get();
        if ($result->isEmpty()) {
            return $result ="Nenhum registro encontrado.";
        }

        
    return response()->json(['message'=> $result ],200);
} catch(\Exception $e){
    return response()->json(['error'=>$e ],422);

}

    }
    // Formulario de Entrada
    public function test(Request $req)
    {
        try {
            // //Valida tipo e se e requirido
            //Valida tipo e se e requirido




                
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


            // $existe = MovPalete::where('TpProc', $validated["formData"]["TpProc"])
            // ->where('NrSerie', $validated["formData"]["NrSerie"])
            // ->where('NrDocumento', $validated["formData"]["NrDocumento"])
            // ->count() > 1; // Verifica se há mais de uma ocorrência
            // if($existe){
            //     return  response()->json(["message"=>  "Essa entrada ja foi cadastrada"]);
            // }


            return  response()->json(['messagem'=>'sucesso']);
            
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
                "formData.DsDestinatario" => "required|string",
                "formData.DsRemetente" => "required|string",

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
                        'message' => 'Essa saida já foi cadastrada.'
                    ], 409);
                }
    

                $palletEntry = MovPalete::create([
                    'NrFun' => trim($req->user()->NrFun),
                    'NrDocumento' => trim($validated['formData']['NrDocumento']),
                    'NrSerie' => trim($validated['formData']['NrSerie']),
                    'TpDoc' => trim($validated['formData']['TpDoc']),
                    'TpOperacao' => trim($validated['formData']['TpOperacao']),
                    'CNPJRemetente' => trim($validated['formData']['CNPJRemetente']),
                    'DsRemetente' => trim($validated['formData']['DsRemetente']),
                    'CNPJDestinatario' => trim($validated['formData']['CNPJDestinatario']),
                    'DsDestinatario' => trim($validated['formData']['DsDestinatario']),
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
        
            MovPaleteSaldoFilial::where('IdFilial', $validated['formData']['FilialRecebedoura'])
            ->update([
                $tipo => DB::raw("$tipo + $qtd"),
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


            

            // Verifica se o retorno da transação é um response e o retorna diretamente
            if ($consulta instanceof \Illuminate\Http\JsonResponse) {
                return $consulta;
            }

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
                "formData.FilialRecebedoura" => "required|string",
                "formData.DsDestinatario" => "required|string",
                "formData.DsRemetente" => "required|string",
                
                "formData.CNPJDestinatario" => "required|string",
                "formData.DataRegistro" =>"required|date",
                "formData.TpProc"=> "required|string",
                'receivedDataFis'=> 'required',
                'totalvalue'=>'required'

            ]);
            

           //se o receivedDataFis = receivedDataDoc ele pega os dados do doc
           $consulta = DB::transaction(function () use ($req,$validated) {
                      
            $existe = MovPalete::where('TpProc', $validated["formData"]["TpProc"])
            ->where('NrSerie', $validated["formData"]["NrSerie"])
            ->where('NrDocumento', $validated["formData"]["NrDocumento"])
            ->count() > 0; // Considera duplicado apenas se houver mais de um
    
            if ($existe) {

                return response()->json([
                    'message' => 'Essa saida já foi cadastrada.'
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
                'DsDestinatario' => trim($validated['formData']['DsDestinatario']),
                'DsRemetente' => trim($validated['formData']['DsRemetente']),
                'FilialRecebedoura' => trim($validated['formData']['FilialRecebedoura']),
                'DataEmissaoDoc'=> Carbon::parse(Carbon::now())->format('d-m-Y H:i'),
                'DataRegistro'=>Carbon::parse($validated['formData']['DataRegistro'])->format('d-m-Y H:i'),
                'TpProc'=>trim($validated['formData']['TpProc'])
                //'created_at' => now(), // Melhor usar o now() para a data atual
            ]);
                     // Inserção dinâmica usando os próprios arrays validados
        foreach ($validated['totalvalue'] as $tipo => $qtd) {
            MovPaleteQtdDoc::create([
                'IdDoc' => $palletEntry->IdDoc,
                'TpPalet' => $tipo,
                'QtdPalete' => $qtd
            ]);
            dump($validated['formData']['CNPJRemetente']);
            dump($tipo);

            dump($qtd);


            MovPaleteSaldoFilial::where('Cnpj', $validated['formData']['CNPJRemetente'])
            ->update([
                $tipo => DB::raw("$tipo - $qtd"),
            ]);
        }

        if($validated['formData']['TpOperacao'] === "DevolPalete"){
            foreach ($validated['receivedDataFis'] as $idDocEntradaFisica => $paletes) {

                foreach ($paletes as $tipoPalet => $quantidade) {
                    if ($quantidade > 0) {
                        $tipoPaletMap = [
                            'devolDescartavel' => 'DESCARTAVEL',
                            'devolCHEP' => 'CHEP',
                            'devolPBR' => 'PBR'
                        ];
            
                        if (isset($tipoPaletMap[$tipoPalet])) {
                            MovPaleteSaldo::where('IdDocEntradaFisica',$idDocEntradaFisica )
                                ->where('TpPalet', $tipoPaletMap[$tipoPalet])
                                ->decrement('QtdPalete', $quantidade);
                            MovVinculoNfSaidaEntrada::create([
                                'IdDocEntrada' => $palletEntry->IdDoc,
                                'IdDocEntradaFisica' => $idDocEntradaFisica,
                                'TpPaletEntrada' => $tipoPaletMap[$tipoPalet],
                                'QtdPaleteSaldo'=> $quantidade
                            ]);

                        };


                    }
                }
            }
        }

           
            return response()->json([
                'message'=>'sucesso',
                "IdDoc" => $palletEntry->IdDoc
        ],201);
        });

                    // Verifica se o retorno da transação é um response e o retorna diretamente
            if ($consulta instanceof \Illuminate\Http\JsonResponse) {
                return $consulta;
            }

            return response()->json([
                'message'=>'sucesso',
                'data'=>$validated
                
        ],201);
        }   catch (QueryException $e) {
            // Verifica se o erro é de restrição CHECK (código 547)
            if ($e->errorInfo[1] == 547) {
                return response()->json([
                    'error' => 'Operação inválida: a quantidade de paletes não pode ser negativa.'
                ], 400);
            }
        
            // Retorna erro genérico para outros problemas de banco
            return response()->json([
                'error' => 'Ocorreu um erro ao atualizar os dados.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
   
     // Formulario de Saida
}
