<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImagenController extends Controller
{
    //
    public function store(Request $request) 
    {
        $imagen = $request->file('file');
        $nombreImagen = Str::uuid() . "." . $imagen->extension();
        $imagenServidor = Image::make($imagen);
        $imagePath = public_path('uploads') . '/' . $nombreImagen;
        
        $imagenServidor->fit(1000,1000);
        $imagenServidor->save($imagePath);

        return response()->json([
            'imagen' => $nombreImagen
        ]);
    }
}
