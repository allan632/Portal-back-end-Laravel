<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\SacCadEvolution;
use App\Models\SacCadOccurrence;
use App\Models\SacNfEvolution;
use App\Models\SacTratOccurrenceNf;
use App\Models\SacTratOccurrence;
use App\Models\Treatment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SacController extends Controller
{
    public function filterEvent(Request $request){
        $rota = $request['rota'];
        $romaneio = "A.CdRomaneio = " . $request["romaneio"];
        $nota = $request['nota'];
        $motorista = $request['motorista'];
        $placa = $request['placa'];
        
        if(!empty($placa)){
            $placa = "AND A.NrPlaca = " .  "'" . $request['placa'] . "'";
        } 
        if(!empty($nota)){
            $nota = "AND D.NrNotaFiscal = " . $request["nota"] ?? null;
        }
        if(!empty($motorista)){
            $motorista = "AND I.DSNOME = " . "'" . $request['motorista'] . "'" ?? null;
        } 

        $sacData = DB::select("select A.CdEmpresa
       ,A.CdRota
       ,A.CdRomaneio
       ,B.CdSequencia
       ,A.NrPlaca
       ,I.DSNOME 'Motorista'
       ,C.CdRemetente
       ,E.DsEntidade 'DsRemetente'
       ,C.CdDestinatario
       ,F.DsEntidade 'DsDestinatario'
       ,H.DsMunicipio
       ,D.NrNotaFiscal
       ,C.NrDoctoFiscal 'CTe'
        from softran_renovacao.dbo.CCERoman A
        inner join softran_renovacao.dbo.CCERomIt B on A.CdEmpresa = B.CdEmpresa and A.CdRota = B.CdRota and A.CdRomaneio = B.CdRomaneio
        inner join softran_renovacao.dbo.GTCConhe C on B.NrSeqControle = C.NrSeqControle
        inner join softran_renovacao.dbo.GTCNFCon D on C.NrSeqControle = D.NrSeqControle and C.CdRemetente = D.CdInscricao and C.CdEmpresa = D.CdEmpresa
        inner join softran_renovacao.dbo.SISCli E on E.CdInscricao = D.CdInscricao
        inner join softran_renovacao.dbo.SISCli F on F.CdInscricao = C.CdDestinatario
        inner join softran_renovacao.dbo.SISCep G on G.CdCep = B.NrCep
        left join softran_renovacao.dbo.SISMun H on G.CdCidade = H.CdMunicipio
        left join softran_renovacao.dbo.GTCFRETE I on I.CDFRETEIRO = A.NrCPFMotorista
        where 1=1 AND {$romaneio} 
        {$nota}
        {$placa}
        {$motorista}
        ORDER BY 4
        ");
       
        return response([
            'message' => 'Success',
            'data' => $sacData
        ]);
    }
 
    public function createEvent(Request $request){
        try {
            $request->validate([
                // 'CdEvento'  => ['required'],
                'CdRotaRom'  => ['required'],
                'CdEmpresaRom'  => ['required'],
                'CdRomaneio'  => ['required'],
                'CdDest'  => ['required'],
                'CdFuncionario'  => ['required'],
                //'InStatus'  => ['required'],
            ]);
            // para garantir atomicidade na criação
            //DB::beginTransaction();
            $eventFound = Event::where('CdRomaneio', $request['CdRomaneio'])
            ->where('CdRotaRom', $request['CdRotaRom'])
            ->where('CdDest', $request['CdDest'])
            ->where('CdEmpresaRom', $request['CdEmpresaRom'])
            ->first();
        
            // $eventFound = Event::find($request['CdRomaneio'] && $request['CdRotaRom'] && $request['CdDest']);
            $lastEvent = Event::latest('CdEvento')->first();
            
            // verificar se existe tratativa para o mesmo remetente e evento
            // se existir, verificar a ocorrência, então criar evolução
            // é mesmo romaneio | destinatario?
            if($eventFound){
                return response()->json([
                    'message' => 'Evento existente',
                    'event' => $eventFound 

                ], Response::HTTP_CONFLICT);
                // se sim -> tratativa, verificar se é a mesma ocorrência da mesma trat
            } 
        
            Event::create([
                'CdEvento'  => $lastEvent['CdEvento'] + 1,
                'CdRotaRom'  => $request['CdRotaRom'],
                'CdEmpresaRom'  => $request['CdEmpresaRom'],
                'CdRomaneio'  => $request['CdRomaneio'],
                'CdDest'  => $request['CdDest'],
                'CdFuncionario'  => $request['CdFuncionario'],
                'InStatus'  => 1,
                'DtEvento' => now()->locale('pt_BR')->format('Y-m-d H:i:s'),
            ]);
            
            // DB::commit();
            return response()->json([
                'message' => 'Evento criado com sucesso',
                'event' => $lastEvent['CdEvento'] + 1 
            ], Response::HTTP_CREATED);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar evento: '. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    // cria-se o evento -> tratativa -> a ocorrência
    // o evento é criado separadamente das ocorrências
    public function createTreatment(Request $request){
        try {
            $request->validate([
                'CdEvento' => 'required',
                'CdRemetente' =>  'required|unique:SacEventoTrat,CdRemetente',
                'CdFuncionario' =>'required'
            ]);
            //$treatmentFound = Treatment::find($request['CdTratativa']);
            
            $treatmentFound = Treatment::where('CdEvento', $request['CdEvento'])
            ->where('CdRemetente', $request['CdRemetente'])
            ->where('CdFuncionario', $request['CdFuncionario'])
            ->first();

            $eventFound = Event::find($request['CdEvento']);
            $lastTreatment = Treatment::latest('CdTratativa')->first();
            
            // evento e remetente disponíveis
            if($treatmentFound){
                if($treatmentFound['CdRemetente'] === $request['CdRemetente']){
                    return response()->json([
                        'message' => 'Tratativa existente para esse remetente'
                    ], Response::HTTP_CONFLICT);
                    // validar se a ocorrencia existe para a tratativa na outra função
                    
                    // se não: criar trativa da ocorrência

                    // createSacTratOccurrenceNf
                    
                    // pular para createOccurrence -> createSacTratOccurrenceNf
                }
            }
          
            if($eventFound){
                Treatment::create([
                    'CdEvento' => $request['CdEvento'],
                    'CdTratativa' => $lastTreatment['CdTratativa'] + 1,
                    'CdRemetente' => $request['CdRemetente'],
                    'CdFuncionario' => $request['CdFuncionario'],
                    'InStatus' => 0,
                    'DtTratativa' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                DB::commit();
                return response()->json([
                    'message' => 'Tratativa criada com sucesso',
                    'data' => $lastTreatment['CdTratativa'] + 1
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'message' => 'Erro ao criar tratativa, evento não existente',
                ], Response::HTTP_BAD_GATEWAY);
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Cria nova nota tratativa da ocorrência -> Ocorrência/tratativa ocorrência nf -> nf evolução -> evolução 
    public function createSacTratOccurrenceNf(Request $request){
        try{
                       // verificar se existe tratativa para o mesmo remetente e evento
            // se existir, verificar a ocorrência, então criar evolução
            $sacTratOccurrenceFound = SacTratOccurrence::find($request['CdTratOcorrencia']);
            $lastTratOccurrenceNf = SacTratOccurrenceNf::latest('CdTratOcorrenciaNf')->first();
            $sacTratOccurrenceNfFound = SacTratOccurrenceNf::find($request['CdTratOcorrenciaNf']);
            
            //$sacTratOccurrenceFound = SacTratOccurrence::where('CdTratOcorrencia', $request['CdTratOcorrencia'])
            // ->where('CdTratativa', $request['CdTratativa'])
            // ->where('CdOcorrencia', $request['CdOcorrencia'])
            // ->first();

            if ($sacTratOccurrenceFound) {
                $sacTratOccurrenceNf = SacTratOccurrenceNf::create([
                    'CdTratOcorrenciaNf' => $lastTratOccurrenceNf['CdTratOcorrenciaNf'] + 1,
                    'CdTratOcorrencia' => $request['CdTratOcorrencia'],
                    'NrSerie' => $request['NrSerie'],
                    'NrNotaFiscal' => $request['NrNotaFiscal'],
                ]);
                $evolutionFound = SacCadEvolution::where('CdEvolucao', $request['CdEvolucao'])
                ->where('DsEvolucao', $request['DsEvolucao'])
                ->where('HrAlerta', $request['HrAlerta'])
                ->first();
                //$evolutionFound = SacCadEvolution::find($request['CdEvolucao']);

                if($evolutionFound){
                    return response()->json([
                        'message' => 'Evolução já existente'
                    ], Response::HTTP_BAD_REQUEST);
                } else {
                    $evolution = SacCadEvolution::create([
                        'CdEvolucao' => $request["CdEvolucao"],
                        'DsEvolucao' => $request["DsEvolucao"],
                        'HrAlerta' => $request["HrAlerta"],
                        'CdFuncionario' => $request["CdFuncionario"],
                        'InAtivo' => 1,
                        'DtCadastro' =>  now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                        'InFinaliza' => 0,
                    ]);

                return response()->json([
                    'message' => 'Criado corretamente', 
                    'ocorrencia' => $sacTratOccurrenceNf,
                    'data' => $evolution
                ], 
                Response::HTTP_CREATED);
            }} else {
                return response()->json([
                    'message' => 'Tratativa de ocorrência não encontrada',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);    
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar ocorrência nf' . $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }  
    // cria a ocorrência, o tratamento da ocorrência, o tratamento da ocorrência NF
    public function createOccurrence(Request $request) {
        try {
            $request->validate([
                'DsOcorrencia' => 'required',
                'NrUsuario' => 'required',
            ]);
            
            try {
                // verifica se há ocorrência e tratamento desta
                $occurrenceFound = SacCadOccurrence::where('DsOcorrencia', $request['DsOcorrencia'])
                ->first();
                $lastOccurrenceFound = SacCadOccurrence::latest('CdOcorrencia')->first();
                $lastTratOccurrence = SacTratOccurrence::latest('CdTratativa')->first();
                $sacTratOccurrenceFound = SacTratOccurrence::where
                ('CdTratativa', $request['CdTratativa'])
                ->first();

                if ($occurrenceFound) {
                    //return response()->json(['message' => 'Ocorrência já existente'], Response::HTTP_CONFLICT);
                    
                    // $sacTratOccurrenceNfFound = SacTratOccurrenceNf::find($request['CdTratOcorrenciaNf']);
                    // cria apenas mais uma linha para nf em sactratocorrenciaNF
                    
                    if (!$sacTratOccurrenceFound) {
                        // Cria tratamento da ocorrência
                        $tratativaOcorrencia = SacTratOccurrence::create([
                            'CdTratOcorrencia' => $occurrenceFound['CdOcorrencia'],
                            'CdTratativa' => $request['CdTratativa'], 
                            'CdOcorrencia' => $request['CdOcorrencia'],
                            'CdFuncionario' => $request['CdFuncionario'],
                            'DtProblema' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                        ]);
                        return response()->json([
                            'message' => 'Ocorrência criada: ',
                            'ocorrencia' => $tratativaOcorrencia
                        ],  Response::HTTP_CREATED);
                    
                    } else {
                        //return response()->json(['message' => 'Tratativa da ocorrência existente '], Response::HTTP_CONFLICT);
                        // criar nf
                        $sacTratOccurrenceFound = SacTratOccurrence::where
                        ('CdTratativa', $request['CdTratativa'])
                        //->where('CdOcorrencia', $request['CdOcorrencia'])
                        ->first();

                        $sacTratOccurrenceNfFound = SacTratOccurrenceNf::where('CdTratOcorrencia', $lastTratOccurrence['CdTratOcorrencia'])
                        // ->where('NrSerie', $request['NrSerie'])
                        ->where('NrNotaFiscal', $request['NrNotaFiscal'])
                        ->first();
                        
                        if(!$sacTratOccurrenceNfFound){
                            $lastTratOccurrenceNf = SacTratOccurrenceNf::latest('CdTratOcorrenciaNf')->first();
                           
                            foreach ($request['NrNotaFiscal'] as $nrNota) {
                                $sacTratOccurrenceNf = SacTratOccurrenceNf::create([
                                    'CdTratOcorrenciaNf' => $lastTratOccurrenceNf['CdTratOcorrenciaNf'] + 1,
                                    'CdTratOcorrencia' => $request['CdTratOcorrencia'],
                                    'NrSerie' => $request['NrSerie'],
                                    'NrNotaFiscal' => $nrNota,
                                ]);
                            }
                            return response()->json(
                                [
                                    'message' => 'Created',
                                    'tratOccurrenceNf' => $sacTratOccurrenceNf
                                ], 
                            Response::HTTP_INTERNAL_SERVER_ERROR);
                        } else {
                            // tem trat ocorrência nf, adiconar apenas nf
                            // SacTratOccurrenceNf::where('NrNotaFiscal', $request['NrNotaFiscal'])
                            // ->update([
                            //     'CdTratOcorrencia' => $request['CdTratOcorrencia']
                            // ]);
                            
                            return response()->json(
                            [
                                'message' => 'Criada nota fiscal'
                            ],
                            Response::HTTP_CREATED);
                        }
                    }
                } else {
                // Cria a nova ocorrência
                $occurrence = SacCadOccurrence::create([
                    'DsOcorrencia' => $request['DsOcorrencia'], 
                    'CdOcorrencia' => $lastOccurrenceFound['CdOcorrencia'] + 1,
                    'NrUsuario' => $request['NrUsuario'],
                    'InAtivo' => 1,
                    'DtCadastro' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                return response()->json(['message' => 'Criado corretamente', 
                'ocorrência' => $occurrence], Response::HTTP_CREATED);
            }

            } catch (\Exception $e) {
                return response()->json(
                    [
                        'message' => 'Erro ao criar ocorrência: ' . $e->getMessage()
                    ], 
                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            // Processa as tratativas de ocorrência
            // foreach ($request["sacTratOcorrencia"] as $sacTratOccurrencia) {
            //     try {
            //         $occurrenceFound = SacCadOccurrence::where('DsOcorrencia', $request['DsOcorrencia'])
            //         ->where('DsOcorrencia', $request['DsOcorrencia'])
            //         ->where('NrUsuario', $request['NrUsuario'])
            //         ->first();

            //         //$occurrenceFound = SacCadOccurrence::find($request->CdOcorrencia);
            //         $lastOccurrenceFound = SacCadOccurrence::latest('CdOcorrencia')->first();

            //         if ($occurrenceFound) {
            //             return response()->json(['message' => 'Ocorrência já existente'], Response::HTTP_CONFLICT);
            //         }
            //         // Cria a nova ocorrência
            //         $occurrence = SacCadOccurrence::create([
            //             'DsOcorrencia' => $lastOccurrenceFound['DsOcorrencia'], 
            //             'CdOcorrencia' => $request['CdOcorrencia'] + 1,
            //             'NrUsuario' => $request['NrUsuario'],
            //             'InAtivo' => $request['InAtivo'],
            //             'DtCadastro' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
            //         ]);
            //         //return response()->json(['message' => 'Criado corretamente', 'ocorrência' => $occurrence], Response::HTTP_CREATED);

            //         $sacTratOccurrenceFound = SacTratOccurrence::find($sacTratOccurrencia['CdTratOcorrencia']);
            //         $lastTratOccurrence = SacCadOccurrence::latest('CdTratOcorrencia')->first();

            //         if (!$sacTratOccurrenceFound) {
            //             // Cria novo tratamento da ocorrência
            //             SacTratOccurrence::create([
            //                 'CdTratOcorrencia' => $lastTratOccurrence['CdTratOcorrencia'] + 1,
            //                 'CdTratativa' => $sacTratOccurrencia['CdTratativa'], 
            //                 'CdOcorrencia' => $occurrence->CdOcorrencia, 
            //                 'CdFuncionario' => $sacTratOccurrencia['CdFuncionario'],
            //                 'DtProblema' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
            //             ]);
            //         } else {
            //             return response()->json(['message' => 'Tratativa da corrência existente '], Response::HTTP_INTERNAL_SERVER_ERROR);

            //         }
            //     } catch (\Exception $e) {
            //         return response()->json(['message' => 'Erro ao criar tratativa de ocorrência: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            //     }
            // }
                
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Erro ao criar ocorrência: ' . $e->getMessage()
                ], 
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
      
    public function createEvolution(Request $request){
        try {
            // $request->validate([
            //     'CdEvolucao' => 'request',
            //     'DsEvolucao' => 'request',
            //     'TpEvol' => 'request',
            //     'TimeEvol' => 'request',
            //     'NrFun' => 'request',
            // ]);
            $evolutionFound = SacCadEvolution::where
            ('CdEvolucao', $request['CdEvolucao'])
            ->first();
            $latestEvolutionFound = SacCadEvolution::latest('CdEvolucao')->first();
    
            if($evolutionFound){
                return response()->json([
                    'message' => 'Evolução já existente'
                ], Response::HTTP_BAD_REQUEST);
            }
            $evolution = SacCadEvolution::create([
                'CdEvolucao' => $latestEvolutionFound->CdEvolucao + 1,
                'DsEvolucao' => $request->DsEvolucao,
                'TpEvol' => $request->TpEvol,
                'TimeEvol' => $request->TimeEvol,
                'NrFun' => $request->NrFun,
                'HrAlerta' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
            ]);
            return response()->json([
                'message' => 'Evolução criado com sucesso', 
                'event' => $evolution
            ], Response::HTTP_CREATED);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar evolução'. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function createSacNfEvolution(Request $request){
        try {
            $request->validate([
                //'CdTratOcorrenciaNf'  => 'required',
                'CdEvolucao'  => 'required',
                'CdFuncionario'  => 'required',
                'DsObs'  => 'required',
            ]);
            //$nfEvolutionFound = SacNfEvolution::find($request['IdTratOcorrenciaNf']);
            $event = Event::find($request['CdEvolucao']);
            $lastNfEvolution = SacNfEvolution::latest('CdTratOcorrenciaNf')->first();

            $nfEvolutionFound = SacNfEvolution::where('CdTratOcorrenciaNf', $request['CdTratOcorrenciaNf'])
            ->where('CdEvolucao', $request['CdEvolucao'])
            ->where('CdFuncionario', $request['CdFuncionario'])
            ->first();
           
            if(!$event){
                return response()->json([
                    'message' => 'Evento não encontrado',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);    
            }
            // verifica se já foi criada a NF
            if($nfEvolutionFound){
                return response()->json([
                    'message' => 'Nf já criada.'
                ], Response::HTTP_CONFLICT);
            } else {
                $nfEvolutionCreated = SacNfEvolution::create([
                    'CdEvolucao' => $request->CdEvolucao,
                    'DsEvolucao' => $request->DsEvolucao,
                    'CdTratOcorrenciaNf' => $lastNfEvolution['CdTratOcorrenciaNf'] + 1,
                    'CdFuncionario' => $request->CdFuncionario,
                    'DsObs' => $request->DsObs,
                    'DtEvolucao'=> now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'message' => 'Nf criada com sucesso', 
                    'nf' => $nfEvolutionCreated
                ], Response::HTTP_CREATED);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar evolução'. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
