<?php

namespace App\Http\Controllers;

use App\Models\PalletEntryExit;
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

    /**
     * Show the form for creating a new resource.
     */
    public function createEntryPallet(Request $req)
    {
        try{
          
        // // Validação
        // $req->validate([
        //     'NrDocumento'=>"required|integer|size:255",
        //     'NrSerie'=>"required|integer|size:255",
        //     "TpDoc"=>"required|string|size:255",
        //     "TpOperacao"=>"required|string|size:255",
        //     "CNPJRemetente"=>"required|string|size:255",
        //     "CNPJDestinatario"=>"required|string|size:255",
        //     "FilialRecebedoura"=>"required|string|size:255",
        //     'FotoDoc' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);
        

        $palletEntry = PalletEntryExit::create([
            'NrFun'=>trim($req->user()->NrFun),
            'NrDocumento'=>trim($req->NrDocumento),
            'NrSerie'=>trim($req->NrSerie),
            'TpDoc'=>trim($req->TpDoc),
            'TpOperacao'=>trim($req->TpOperacao),
            'CNPJRemetente'=>trim($req->CNPJRemetente),
            'CNPJDestinatario'=>trim($req->CNPJDestinatario),
            'FilialRecebedoura'=>trim($req->FilialRecebedoura),
            'created_at' => '14-02-2025 15:30:00',
            
        ]);

   

        // 🔹 Salvar a imagem no storage
        // if ($req->hasFile('FotoDoc')) {
        //     $image = $req->file('FotoDoc');
        //     $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension(); // Gera um nome único
            
        //     // Salvar no diretório "public/uploads"
        //     $path = $image->storeAs('uploads', $imageName, 'public');
        
        //     // 🔹 Se quiser salvar no banco, crie um Model e salve a URL
        //     $imageModel = new FtDocAuxiliar();
        //     $imageModel->filename = $imageName;
        //     $imageModel->path = '/storage/' . $path;
        //     $imageModel->save();

        //     return response()->json([
        //         'message' => 'Imagem enviada com sucesso!',
        //         'file_path' => asset('storage/' . $path)
        //     ], 201);
        // }
                
                return response()->json(["message"=> $palletEntry ], 201);
            } catch( \Exception $e) {
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
