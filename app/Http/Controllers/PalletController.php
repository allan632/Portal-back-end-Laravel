<?php

namespace App\Http\Controllers;

use App\Models\PalletEntryExit;
use Illuminate\Http\Request;


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
          

        


                
                return response()->json($req, 201);
            } catch( \Exception $e) {
            return response()->json(["message"=>"erro"]);

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
