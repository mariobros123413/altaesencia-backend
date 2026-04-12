<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleNoteStoreRequest;
use App\Http\Resources\SaleNoteResource;
use App\Models\Sale;

class SaleNoteController extends Controller
{
    public function store(SaleNoteStoreRequest $request, Sale $sale): SaleNoteResource
    {
        $note = $sale->notes()->create([
            'user_id' => $request->validated('user_id'),
            'note_type' => $request->validated('note_type', 'interna'),
            'note' => $request->validated('note'),
            'estado' => $request->validated('estado', 'activo'),
        ]);

        return new SaleNoteResource($note->load('user'));
    }
}
