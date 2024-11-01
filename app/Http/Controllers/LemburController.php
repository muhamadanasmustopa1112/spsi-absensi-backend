<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LemburResource;

class LemburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = $request->input('company_id');
        
        $data = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
            $query->where('id', $company_id);
        })->get();

        return response()->json([
            'success' => true,
            'data' => LemburResource::collection($data),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'description' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $lembur = Lembur::create([
            'companies_users_id' => $request->companies_users_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'description' => $request->description,
            'status' => $request->status,       
        ]);

        if($lembur){
            return response()->json([
                'status' => true,
                'message' => 'success',
            ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'error created',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lembur = Lembur::find($id);

        if(!$lembur){
            return response()->json([
                'status' => 'error',
                'data' => $lembur,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' =>  new LemburResource($lembur),
                'message' => 'Success get data lembur'
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         
         $validator = Validator::make($request->all(), [
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'description' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $lembur = Lembur::find($id);

        if (!$lembur) {
            return response()->json(['message' => 'Lembur User not found'], 404);
        }

        $lembur->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data Lembur berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lembur = Lembur::findOrFail($id); 
            $lembur->delete(); 
            
            return response()->json([
                'success' => true,
                'message' => 'lembur deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'lembur not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lembur',
            ], 500);
        }
    }

    public function getLemburWhereCompanyUser($id)
    {
        try {
           $data = Lembur::where('companies_users_id', $id)->get();

            return response()->json([
                'success' => true,
                'data' => LemburResource::collection($data),
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lembur not found',
            ], 404);
        }
    }
}
