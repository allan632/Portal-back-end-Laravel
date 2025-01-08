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
    // Filtro principal para dados de busca de romaneio
    public function filterEvent(Request $request){
        $rota = $request['rota'];
        $nota = $request['nota'];
        $motorista = $request['motorista'];
        $placa = $request['placa'];
        $romaneio = $request['romaneio'];
        $cte = $request['cte'];
        
        if(!empty($placa)){
            $placa = "AND A.NrPlaca = " . "'" . $request['placa']. "'";
        } 
        if(!empty($nota)){
            $nota = "AND D.NrNotaFiscal = " . $request["nota"] ?? null;
        }
        if(!empty($motorista)){
            $motorista = "AND I.DSNOME = " . "'" . $request['motorista'] . "'" ?? null;
        } 
        if(!empty($romaneio)){
            $romaneio = "AND A.CdRomaneio = " ."'" . $request["romaneio"]. "'" ;
        }  if(!empty($cte)){
            $cte = "AND C.NrDoctoFiscal = " ."'" . $request["cte"]. "'" ;
        }
        $sacData = DB::select("select A.CdEmpresa
           ,A.CdRota
           ,A.CdRomaneio
           ,A.NrPlaca
           ,A.DtSaida
           ,B.CdSequencia
           ,D.NrSerie
           ,C.NrDoctoFiscal 'CTe'
           ,C.CdRemetente
           ,C.CdDestinatario
           ,D.NrNotaFiscal
           ,E.DsEntidade 'DsRemetente'
           ,F.DsEntidade 'DsDestinatario'
           ,H.DsMunicipio
           ,I.DSNOME 'Motorista'
           --,D.NrSerie
            from softran_renovacao.dbo.CCERoman A
            inner join softran_renovacao.dbo.CCERomIt B on A.CdEmpresa = B.CdEmpresa and A.CdRota = B.CdRota and A.CdRomaneio = B.CdRomaneio
            inner join softran_renovacao.dbo.GTCConhe C on B.NrSeqControle = C.NrSeqControle and C.CdEmpresa = B.CdEmpresaColetaEntrega
            inner join softran_renovacao.dbo.GTCNFCon D on C.NrSeqControle = D.NrSeqControle and C.CdRemetente = D.CdInscricao and C.CdEmpresa = D.CdEmpresa
            inner join softran_renovacao.dbo.SISCli E on E.CdInscricao = D.CdInscricao
            inner join softran_renovacao.dbo.SISCli F on F.CdInscricao = C.CdDestinatario
            inner join softran_renovacao.dbo.SISCep G on G.CdCep = B.NrCep
            left join softran_renovacao.dbo.SISMun H on G.CdCidade = H.CdMunicipio
            left join softran_renovacao.dbo.GTCFRETE I on I.CDFRETEIRO = A.NrCPFMotorista
            where 1=1 {$romaneio} 
            {$nota}
            {$placa}
            {$motorista}
            {$cte}
            ORDER BY 4
            ");
            // ORDER BY DsDestinatario [ASC|DESC]
            return response([
                'message' => 'Success',
                'data' => $sacData
            ]);
    }
    // Busca as ocorrências existentes
    public function getOccurrence(){
        try {
            $occurrences = SacCadOccurrence::pluck('DsOcorrencia', 'CdOcorrencia')->toArray();
            
            return response()->json([
                'message' => 'Success',
                'occurrences' => $occurrences,
            ], Response::HTTP_ACCEPTED);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao buscar dados: '. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Busca os dados da tabela com listagem baseada no romaneio
    public function getNewData(Request $request){
        $nota = $request['nota'];
        $motorista = $request['motorista'];
        $placa = $request['placa'];
        $romaneio = $request['romaneio'];
        $cte = $request['cte'];
        
        if(!empty($placa)){
            $placa = "AND A.NrPlaca = " . "'" . $request['placa']. "'";
        } 
        if(!empty($nota)){
            $nota = "AND D.NrNotaFiscal = " . $request["nota"] ?? null;
        }
        if(!empty($motorista)){
            $motorista = "AND I.DSNOME = " . "'" . $request['motorista'] . "'" ?? null;
        }
        if(!empty($romaneio)){
            $romaneio = "AND A.CdRomaneio = " ."'" . $request["romaneio"]. "'" ;
        }
        try{
            $data = DB::select(("select 
                A.DtSaida
                ,A.CdEmpresa
                ,A.CdRota
                ,A.CdRomaneio
                ,A.NrPlaca
                ,I.DSNOME 'Motorista'
                from softran_renovacao.dbo.CCERoman A
                inner join softran_renovacao.dbo.CCERomIt B on A.CdEmpresa = B.CdEmpresa and A.CdRota = B.CdRota and A.CdRomaneio = B.CdRomaneio
                inner join softran_renovacao.dbo.GTCConhe C on B.NrSeqControle = C.NrSeqControle and C.CdEmpresa = B.CdEmpresaColetaEntrega
                inner join softran_renovacao.dbo.GTCNFCon D on C.NrSeqControle = D.NrSeqControle and C.CdRemetente = D.CdInscricao and C.CdEmpresa = D.CdEmpresa
                inner join softran_renovacao.dbo.SISCli E on E.CdInscricao = D.CdInscricao
                inner join softran_renovacao.dbo.SISCli F on F.CdInscricao = C.CdDestinatario
                inner join softran_renovacao.dbo.SISCep G on G.CdCep = B.NrCep
                left join softran_renovacao.dbo.SISMun H on G.CdCidade = H.CdMunicipio
                left join softran_renovacao.dbo.GTCFRETE I on I.CDFRETEIRO = A.NrCPFMotorista
                WHERE 1=1
                AND A.DtSaida >= getdate() - 10
                AND A.DtChegada is null
                {$romaneio} 
                {$nota}
                {$placa}
                {$motorista}
                {$cte}
                group by A.DtSaida,A.CdEmpresa,A.CdRota,A.CdRomaneio,A.NrPlaca,I.DSNOME
            "));
    
            return response()->json([
               'data' => $data
            ], Response::HTTP_OK);
        } catch(\Exception $e){
            return response()->json([
                'message' => "Ocorreu um erro interno" . $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Busca os dados filtrados para a tabela inicial, com filtro de data
    public function getDataControl(Request $request){
        $initialDate = $request['initialDate'];
        $lastDate = $request['lastDate'];

        if(!empty($initialDate)){
            $initialDate = "AND C.DtProblema >= " . "'" . $request['initialDate']. "'";
        } 
        if(!empty($lastDate)){
            $lastDate = "AND F.DtEvolucao <= " . "'" . $request['lastDate'] . "'" ?? null;
        } 
        $data = DB::select("select   A.CdEvento
        ,A.CdDest
        ,I.DsEntidade 'Destinatario'
        ,B.CdTratativa
        ,MAX(DtEvolucao) as 'DsEvol'
        ,B.CdRemetente
        ,J.DsEntidade 'EmbarcadorRemetente'
        ,D.DsOcorrencia
        ,D.InAtivo 'SituacaoOcorrencia'
        ,C.DtProblema 'DataCadOcorrencia'
        ,H.DsNome 'FuncCadOcorrencia'
        ,Max(F.DtEvolucao) 'DtUltimaEvolucao'
        ,DATEDIFF(MINUTE,  C.DtProblema, GETDATE()) AS MinutosPassados
        ,DATEDIFF(MINUTE,  F.DtEvolucao, GETDATE()) AS MinutosEvolucao
        ,K.NrPlaca
        ,L.DSNOME 'Motorista'
        ,E.CdTratOcorrenciaNf
        ,F.DsObs
		,E.NrNotaFiscal
        from SacEvento A
            inner join SacEventoTrat B on B.CdEvento = A.CdEvento
            left join SacTratOcorrencia C ON C.CdTratativa = B.CdTratativa
            left join SacCadOcorrencia D on D.CdOcorrencia = C.CdOcorrencia
            left join SacTratOcorrenciaNf E on E.CdTratOcorrencia= C.CdTratOcorrencia
            left join SacNfEvolucao F on F.CdTratOcorrenciaNf = E.CdTratOcorrenciaNf
            left join SacCadEvolucao G on G.CdEvolucao = F.CdEvolucao
            left join softran_renovacao.dbo.SISFun H on H.CdFuncionario = C.CdFuncionario
            LEFT join softran_renovacao.dbo.SISCli I ON A.CdDest = I.CdInscricao
            LEFT join softran_renovacao.dbo.SISCli J ON B.CdRemetente = J.CdInscricao
            left join softran_renovacao.dbo.CCERoman K on A.CdEmpresaRom = K.CdEmpresa and A.CdRotaRom = K.CdRota and A.CdRomaneio = K.CdRomaneio
            left join softran_renovacao.dbo.GTCFRETE L on L.CDFRETEIRO = K.NrCPFMotorista
        WHERE 1=1
        {$initialDate} 
        {$lastDate}
        group by A.CdEvento, F.DtEvolucao,E.NrNotaFiscal, B.CdTratativa, E.CdTratOcorrenciaNf, F.DsObs, D.DsOcorrencia, D.InAtivo, C.DtProblema, H.DsNome,A.CdDest ,I.DsEntidade,B.CdRemetente,J.DsEntidade,K.NrPlaca,L.DSNOME
        ");
        $evolutions = SacCadEvolution::pluck('DsEvolucao', 'CdEvolucao')->toArray();
        $datas = SacNfEvolution::pluck('DtEvolucao')->toArray();
        // $lastEvolution = DB::select("
        // select    A.CdEvento
        // ,A.CdDest
        // ,I.DsEntidade 'Destinatario'
        // ,B.CdTratativa
        // ,B.CdRemetente
        // ,J.DsEntidade 'EmbarcadorRemetente'
        // ,D.DsOcorrencia
        // ,D.InAtivo 'SituacaoOcorrencia'
        // ,C.DtProblema 'DtCadOcorrencia'
        // ,H.DsNome 'FuncCadOcorrencia'
        // ,Max(F.DtEvolucao) 'DtUltimaEvolucao'
        // ,DATEDIFF(MINUTE,  C.DtProblema, GETDATE()) AS MinutosPassados
        // ,E.NrNotaFiscal 'nota'
        // ,F.DsObs
        // ,C.CdOcorrencia
        // ,E.CdTratOcorrenciaNf
        // ,G.HrAlerta
        // from SacEvento A
        //     inner join SacEventoTrat B on B.CdEvento = A.CdEvento
        //     left join SacTratOcorrencia C ON C.CdTratativa = B.CdTratativa
        //     left join SacCadOcorrencia D on D.CdOcorrencia = C.CdOcorrencia
        //     left join SacTratOcorrenciaNf E on E.CdTratOcorrencia= C.CdTratOcorrencia
        //     left join SacNfEvolucao F on F.CdTratOcorrenciaNf = E.CdTratOcorrenciaNf
        //     left join SacCadEvolucao G on G.CdEvolucao = F.CdEvolucao
        //     left join softran_renovacao.dbo.SISFun H on H.CdFuncionario = C.CdFuncionario
        //     LEFT join softran_renovacao.dbo.SISCli I ON A.CdDest = I.CdInscricao
        //     LEFT join softran_renovacao.dbo.SISCli J ON B.CdRemetente = J.CdInscricao
        // WHERE 1=1
        // and F.DtEvolucao=(select MAX(DtEvolucao) from SacNfEvolucao where CdTratOcorrenciaNf = {$request['CdTratOcorrenciaNf']})
        // group by A.CdEvento, G.HrAlerta, E.CdTratOcorrenciaNf,E.NrNotaFiscal,F.DsObs,B.CdTratativa, C.CdOcorrencia, D.DsOcorrencia, D.InAtivo, C.DtProblema, H.DsNome,A.CdDest ,I.DsEntidade,B.CdRemetente,J.DsEntidade    
       
        // ");

        return response()->json([
            "occurrences" => $data,
            'evolutions' => $evolutions,
            'datas' => $datas,
            
        ]);
    }
    // dados iniciais para tabela sem filtro
    public function filterControl(Request $request){
        $data = DB::select("select    A.CdEvento
        ,A.CdDest
        ,I.DsEntidade 'Destinatario'
        ,B.CdTratativa
        ,B.CdRemetente
        ,J.DsEntidade 'EmbarcadorRemetente'
        ,D.DsOcorrencia
        ,D.InAtivo 'SituacaoOcorrencia'
        ,C.DtProblema 'DtCadOcorrencia'
        ,H.DsNome 'FuncCadOcorrencia'
        ,Max(F.DtEvolucao) 'DtUltimaEvolucao'
        ,DATEDIFF(MINUTE,  C.DtProblema, GETDATE()) AS MinutosPassados
        ,DATEDIFF(MINUTE,  F.DtEvolucao, GETDATE()) AS MinutosEvolucao
        ,E.NrNotaFiscal 'nota'
        ,F.DsObs
        ,C.CdOcorrencia
        ,E.CdTratOcorrenciaNf
        ,G.HrAlerta
        --,F.DtEvolucao
        from SacEvento A
            inner join SacEventoTrat B on B.CdEvento = A.CdEvento
            left join SacTratOcorrencia C ON C.CdTratativa = B.CdTratativa
            left join SacCadOcorrencia D on D.CdOcorrencia = C.CdOcorrencia
            left join SacTratOcorrenciaNf E on E.CdTratOcorrencia= C.CdTratOcorrencia
            left join SacNfEvolucao F on F.CdTratOcorrenciaNf = E.CdTratOcorrenciaNf
            left join SacCadEvolucao G on G.CdEvolucao = F.CdEvolucao
            left join softran_renovacao.dbo.SISFun H on H.CdFuncionario = C.CdFuncionario
            LEFT join softran_renovacao.dbo.SISCli I ON A.CdDest = I.CdInscricao
            LEFT join softran_renovacao.dbo.SISCli J ON B.CdRemetente = J.CdInscricao
        WHERE 1=1
        --AND D.DsOcorrencia = '{$request['DsObs']}'
        --AND  C.CdTratativa ={$request['CdTratativa']} AND C.CdOcorrencia={$request['CdOcorrencia']}
        group by A.CdEvento, F.DtEvolucao,G.HrAlerta, E.CdTratOcorrenciaNf,E.NrNotaFiscal,F.DsObs,B.CdTratativa, C.CdOcorrencia, D.DsOcorrencia, D.InAtivo, C.DtProblema, H.DsNome,A.CdDest ,I.DsEntidade,B.CdRemetente,J.DsEntidade    ");
        
        $evolutions = SacCadEvolution::pluck('DsEvolucao', 'CdEvolucao')->toArray();
        // SELECT MAX(DtEvolucao) FROM SacNfEvolucao where CdTratOcorrenciaNf = 2 
        return response()->json([
            "occurrences" => $data,
            'evolutions' => $evolutions,
        ]);
    }
    // criação do evento
    public function createEvent(Request $request){
        try {
            $request->validate([
                'CdRotaRom'  => ['required'],
                'CdEmpresaRom'  => ['required'],
                'CdRomaneio'  => ['required'],
                'CdDest'  => ['required'],
                'CdFuncionario'  => ['required'],
            ]);
            $eventFound = Event::where('CdRomaneio', $request['CdRomaneio'])
            ->where('CdRotaRom', $request['CdRotaRom'])
            ->where('CdDest', $request['CdDest'])
            ->where('CdEmpresaRom', $request['CdEmpresaRom'])
            ->first();
            // verificar se existe tratativa para o mesmo remetente e evento
            // se existir: cria tratativa e verificar a ocorrência, então criar evolução
            if($eventFound){
                $sacTreatment = Treatment::where('CdEvento', $eventFound['CdEvento'])
                ->first();
                
                if(!$sacTreatment){
                    return response()->json([
                        'message' => 'Tratativa não existente',
                        'event' => $eventFound['CdEvento']  
                    ], Response::HTTP_OK);
                    
                } else {
                    return response()->json([
                        'message' => 'Tratativa existente',
                        'event' => $eventFound['CdEvento'],
                        'treatment'=> $sacTreatment
                    ], Response::HTTP_OK);
                }
            } else {
                $lastEvent = Event::latest('CdEvento')->first();
        
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
                
                return response()->json([
                    'message' => 'Evento criado com sucesso',
                    'event' => $lastEvent['CdEvento'] + 1, 
                    'eventFound' => $eventFound
                ], Response::HTTP_CREATED);   
            }
           
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar evento: '. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // o evento é criado separadamente das ocorrências
    public function createTreatment(Request $request){
        try {
            $request->validate([
                'CdEvento' => 'required',
                'CdRemetente' =>  'required',
                'CdFuncionario' =>'required'
            ]);
            $eventFound = Event::where('CdEvento', $request['CdEvento'])->first();

            if($eventFound){
                // função lastest busca o último elemento cadastrado na tabela, utilizado para gerar o ID
                $lastTreatment = Treatment::latest('CdTratativa')->first();
                $treatmentFound = Treatment::where('CdEvento', $request['CdEvento'])
                ->where('CdRemetente', $request['CdRemetente'])
                ->where('CdFuncionario', $request['CdFuncionario'])
                ->first();

                if(!$treatmentFound){
                    Treatment::create([
                        'CdEvento' => $eventFound['CdEvento'],
                        'CdTratativa' => $lastTreatment['CdTratativa'] + 1,
                        'CdRemetente' => $request['CdRemetente'],
                        'CdFuncionario' => $request['CdFuncionario'],
                        'InStatus' => 1,
                        'DtTratativa' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                    ]);
                    $treatmentFound = Treatment::where('CdTratativa', $lastTreatment['CdTratativa'] + 1)
                    ->first();

                    return response()->json([
                        'message' => 'Tratativa criada com sucesso',
                        'treatment' => $treatmentFound
                    ], Response::HTTP_CREATED);
                } else {
                    $sacTratOccurrenceFound = SacTratOccurrence::find($treatmentFound['CdTratativa']);
                    if(!$sacTratOccurrenceFound){
                        return response()->json([
                            'message' => 'Tratativa da ocorrência não existente.',
                            'treatment' => $treatmentFound
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'message' => 'Tratativa da ocorrência existente.',
                            'treatment' => $treatmentFound
                        ], Response::HTTP_OK);
                    }
                    return response()->json([
                        'message' => 'Tratativa existente.',
                        'treatment' => $treatmentFound
                    ], Response::HTTP_OK);
                }
            } else {
                return response()->json([
                    'message' => 'Evento não existente.',
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // cria a evolução
    public function createEvolution(Request $request){
        try {
            $request->validate([
                'DsEvolucao' => 'required',
                'CdFuncionario' => 'required',
            ]);
            $evolutionFound = SacCadEvolution::where
            ('DsEvolucao', $request['DsEvolucao'])
            ->first();
            $latestEvolutionFound = SacCadEvolution::latest('CdEvolucao')->first();
    
            if($evolutionFound){
                return response()->json([
                    'message' => 'Evolução já existente'
                ], Response::HTTP_CONFLICT);
            } else {
                SacCadEvolution::create([
                    'CdEvolucao' => $latestEvolutionFound->CdEvolucao + 1,
                    'DsEvolucao' => $request->DsEvolucao,
                    'CdFuncionario' => $request->CdFuncionario,
                    'HrAlerta' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                    'InFinaliza' => 0
                ]);
                return response()->json([
                    'message' => 'Evolução criado com sucesso', 
                    'event' => $latestEvolutionFound->CdEvolucao + 1
                ], Response::HTTP_CREATED);
            }

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar evolução'. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // cria a ocorrência, o tratamento da ocorrência, o tratamento da ocorrência NF
    public function createOccurrence(Request $request){
        try {
            $request->validate([
                'DsOcorrencia' => 'required',
                'NrUsuario' => 'required',
            ]);
            $occurrenceFound = SacCadOccurrence::where('DsOcorrencia', $request['DsOcorrencia'])->first();
            if(!$occurrenceFound){
                $lastOccurrenceFound = SacCadOccurrence::latest('CdOcorrencia')->first();

                SacCadOccurrence::create([
                    'DsOcorrencia' => $request['DsOcorrencia'], 
                    'CdOcorrencia' => $lastOccurrenceFound['CdOcorrencia'] + 1,
                    'NrUsuario' => $request['NrUsuario'],
                    'InAtivo' => 1,
                    'DtCadastro' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                $occurrenceCreated = SacCadOccurrence::where('DsOcorrencia', $lastOccurrenceFound['CdOcorrencia'] + 1)->first();

                return response()->json([
                    'message' => 'Ocorrência criada com sucesso',
                    'ocorrencia' => $occurrenceCreated
                ],  Response::HTTP_CREATED);
            } else {
                $occurrenceFound = SacCadOccurrence::where('DsOcorrencia', $request['DsOcorrencia'])->first();

                return response()->json([
                    'message' => 'Ocorrência já criada.',
                    'ocorrencia' => $occurrenceFound
                ], 
                Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Erro ao criar ocorrência: ' . $e->getMessage()
                ], 
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
    }
    // Função de criação do tratamento de ocorrência
    public function createSacTratOccurrence(Request $request){    
        try {
            $request->validate([
                'CdTratativa' => 'required',
                'CdOcorrencia' => 'required',
                'CdFuncionario' => 'required',
                'NrNotaFiscal' => 'required',
            ]);
            // Busca tratamento da ocorrêencia e a ocorrência
            $sacTratOccurrenceFound = SacTratOccurrence::where('CdTratativa', $request['CdTratativa'])
            ->where('CdOcorrencia', $request['CdOcorrencia'])
            ->first();
            $lastTratOccurrence = SacTratOccurrence::latest('CdTratOcorrencia')->first();
            $occurrenceFound = SacCadOccurrence::where('CdOcorrencia', $request['CdOcorrencia'])->first();

            // buscar com base na descrição da ocorrência / sacTratOcorrenciaNf tem várias sacTratOcorrencia
          
            $treatmentFound = Treatment::where('CdTratativa', $request['CdTratativa'])->first();
            if (!$sacTratOccurrenceFound) {
                
                if($occurrenceFound && $treatmentFound){
                $tratativaOcorrencia = SacTratOccurrence::create([
                    'CdTratOcorrencia' => $lastTratOccurrence['CdTratOcorrencia'] + 1,
                    'CdTratativa' => $request['CdTratativa'],
                    'CdOcorrencia' => $request['CdOcorrencia'],
                    'CdFuncionario' => $request['CdFuncionario'],
                    'DtProblema' => now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'message' => 'Ocorrência criada',
                    'ocorrencia' => $tratativaOcorrencia
                ],  Response::HTTP_CREATED);
            
                } else {
                    return response()->json([
                        'message' => 'Ocorrência ou tratativa não existentes.',
                        'nf' => $occurrenceFound,
                        'treatmentFound' => $treatmentFound
                    ], Response::HTTP_OK);
                }
            } else {
                $sacTratOccurrenceFound = SacTratOccurrence::where('CdTratativa', $request['CdTratativa'])
                ->where('CdOcorrencia', $request['CdOcorrencia'])
                ->first();
                return response()->json([
                    'message' => 'Tratativa da ocorrência existente',
                    'ocorrencia' => $sacTratOccurrenceFound
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar tratativa da ocorrência: '. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Cria nova nota tratativa da ocorrência -> Ocorrência/tratativa ocorrência nf -> nf evolução -> evolução 
    public function createSacTratOccurrenceNf(Request $request){
        try{
            $request->validate([
                'CdTratOcorrencia' => 'required',
                'NrSerie' => 'required',
                'NrNotaFiscal' => 'required',
            ]);
            // verificar se existe tratativa para o mesmo remetente e evento e se existir, verificar a ocorrência, então criar evolução
            $lastTratOccurrenceNf = SacTratOccurrenceNf::latest('CdTratOcorrenciaNf')->first();
            $sacTratOccurrenceNfFound = SacTratOccurrenceNf::where('NrNotaFiscal', $request['NrNotaFiscal'])
            ->where('CdTratOcorrencia', $request['CdTratOcorrencia'])
            ->get();

            // se não existir nota ou se existir a nota, mas com uma tratativa de ocorrência diferente
            if (empty($sacTratOccurrenceNfFound) || count($sacTratOccurrenceNfFound) === 0) {
                SacTratOccurrenceNf::create([
                    'CdTratOcorrenciaNf' => $lastTratOccurrenceNf['CdTratOcorrenciaNf'] + 1,
                    'CdTratOcorrencia' => $request['CdTratOcorrencia'],
                    'NrSerie' => $request['NrSerie'],
                    'NrNotaFiscal' => $request['NrNotaFiscal'],
                ]);
                
                return response()->json([
                    'message' => 'Tratativa da ocorrência criada com sucesso.', 
                    'tratativaOcorrencia' => $lastTratOccurrenceNf['CdTratOcorrenciaNf'] + 1,
                    'nf' => $request['NrNotaFiscal'],
                    'sacTratOccurrenceFound' => $sacTratOccurrenceNfFound
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'message' => 'Tratativa de ocorrência já existente.',
                    'nf' => $request['NrNotaFiscal'],
                    'sacTratOccurrenceFound' => $sacTratOccurrenceNfFound
                ], Response::HTTP_OK);    
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao criar ocorrência nf.' . $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }      
    public function createSacNfEvolution(Request $request){

        // atualizar o DtEvolucao
        try {
            $request->validate([
                'CdEvolucao'  => 'required',
                'CdTratOcorrenciaNf' => 'required',
                'CdFuncionario'  => 'required',
                'DsObs'  => 'required',
            ]);
            // $sacTratOccurrenceFound = SacTratOccurrence::where('CdTratativa', $request['CdTratativa']
            // ->where("CdOcorrencia", $request['CdOcorrencia'])
            // )->get();
            $evolution = SacCadEvolution::where('CdEvolucao', $request['CdEvolucao'])->first();
            $lastNfEvolution = SacNfEvolution::latest('CdTratOcorrenciaNf')->first();
            $nfEvolutionFound = SacNfEvolution::where('CdTratOcorrenciaNf', $request['CdTratOcorrenciaNf'])
            ->where('DsObs', $request['DsObs'])
            ->first();
            // $evolution = SacCadEvolution::where('CdEvolucao', $request->get('CdEvolucao', null))->first();

            if(!$nfEvolutionFound){
                if(!$evolution){
                    return response()->json([
                        'message' => 'Evolução não encontrada.',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);    
                }
                $nfEvolutionCreated = SacNfEvolution::create([
                    'CdEvolucao' => $request->CdEvolucao,
                    'CdTratOcorrenciaNf' => $request->CdTratOcorrenciaNf,
                    'CdFuncionario' => $request->CdFuncionario,
                    'DsObs' => $request->DsObs,
                    'DtEvolucao'=> now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'message' => 'Criada com sucesso.', 
                    'nf' => $nfEvolutionCreated
                ], Response::HTTP_CREATED);
            } else {
            //    $nfEvolutionFound->DtEvolucao = now()->setTimezone('America/Sao_Paulo')->format('Y-m-d H:i:s');

                return response()->json([
                    'message' => 'Nf da evolução já criada.'
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar evolução'. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // public function filterOccurrence(Request $request){
    //     $initialDate = $request['initialDate'];
    //     $lastDate = $request['lastDate'];

    //     if(!empty($initialDate)){
    //         $initialDate = "AND SacEvento.DtEvento = " . "'" . $request['initialDate']. "'";
    //     } 
    //     if(!empty($lastDate)){
    //         $lastDate = "AND SacCadEvolucao.DtCadastro = " . "'" . $request['lastDate'] . "'" ?? null;
    //     } 
    //     try {
    //         $data = DB::select("select SacEvento.CdFuncionario
    //         , SacEvento.DtEvento
    //         , SacEvento.InStatus
    //         , SacCadOcorrencia.DsOcorrencia
    //         , SacNfEvolucao.DtEvolucao
    //         , SacCadEvolucao.DtCadastro 
    //         , SacEventoTrat.CdFuncionario
    //         , UsuPor.NrFun
    //         from SacEvento
    //         left join SacEventoTrat on SacEventoTrat.CdEvento = SacEvento.CdEvento
    //         left join SacTratOcorrencia ON SacTratOcorrencia.CdTratativa = SacEventoTrat.CdTratativa
    //         left join SacCadOcorrencia on SacCadOcorrencia.CdOcorrencia = SacTratOcorrencia.CdOcorrencia
    //         left join SacTratOcorrenciaNf on SacTratOcorrenciaNf.CdTratOcorrencia= SacTratOcorrencia.CdTratOcorrencia
    //         left join SacNfEvolucao on SacNfEvolucao.CdTratOcorrenciaNf = SacTratOcorrenciaNf.CdTratOcorrenciaNf
    //         left join SacCadEvolucao on SacCadEvolucao.CdEvolucao = SacNfEvolucao.CdEvolucao
    //         left join UsuPor on UsuPor.NrFun = SacEventoTrat.CdFuncionario
    //         WHERE 1=1
    //         {$initialDate} 
    //         ");
        
    //         return response()->json([
    //             'message' => "Dados encaminhados com sucesso.",
    //             'data' => $data
    //         ], Response::HTTP_OK);
    //     } catch(\Exception $e){
    //         return response()->json([
    //             'message' => "Erro interno: " . $e
    //         ], Response::HTTP_BAD_GATEWAY);
    //     }
    // }
    
}
