<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => ['required', 'unique:users,username,'.auth()->user()->id, 'min:3', 'max:20', 'not_in:twitter,editar-perfil'],
            'email' => 'required|email|unique:users,imagen,'.auth()->user()->id,
        ]);

        if($request->imagen) {
            $imagen = $request->file('imagen');

            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            $imagenServidor = Image::make($imagen);
            $imagenServidor->fit(1000, 1000);

            $imagenPath = public_path('perfiles') . "/" . $nombreImagen;
            $imagenServidor->save($imagenPath);
        }

        //Guardar Cambios

        $usuario = User::find(auth()->user()->id);
        $usuario->username = Str::slug($request->username);
        $usuario->email = $request->email ?? auth()->user()->email;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;

        $usuario->save();
        
        if($request->oldpassword || $request->password) {
            $this->validate($request, [
                'password' => 'required|confirmed|different:oldpassword',
            ]);

            if (Hash::check($request->oldpassword, auth()->user()->password)) {
                $usuario->password = Hash::make($request->password) ?? auth()->user()->password;
                $usuario->save();
            } else {
                return back()->with('mensaje', 'La Contraseña Actual no Coincide. 
                El resto de los cambios han sido guardados.');
            }
        }
        
        return redirect()->route('posts.index', $usuario->username);
    }
    
    // public function store(Request $request)
    // {

    //     if($request->password){
    //         if(!auth()->attempt(['email' => auth()->user()->email, 'password' => $request->password])){
    //             return back()->with('mensaje', 'Password Incorrecta');
    //         }
    //     }

    //     $request->request->add(['username' => Str::slug( $request->username )]);
        
    //     //Otra forma de validar cuando hay mas de 3 reglas
    //     $this->validate($request, [
    //         'username' => ['required', 'unique:users,username,'.auth()->user()->id,'min:3','max:20', 'not_in:editar-perfil,register,logout,imagenes'],
    //         'email' => ['required','unique:users,email,'.auth()->user()->email,'email','max:60'],
    //         'password_nueva' => $request->password ? 'min:6' : '',
    //     ]);        

    //     if($request->imagen){
    //         $imagen = $request->file('imagen');
    //     $nombreImagen = Str::uuid() . "." . $imagen->extension();
    //     $imagenServidor = Image::make($imagen);
    //     $imagePath = public_path('perfiles') . '/' . $nombreImagen;
        
    //     $imagenServidor->fit(1000,1000);
    //     $imagenServidor->save($imagePath);
    //     } 

    //     $usuario = User::find(auth()->user()->id);
    //     $usuario->username = $request->username ?? auth()->user()->username;
    //     $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
    //     $usuario->email = $request->email ?? auth()->user()->email;
        
    //     if($request->password_nueva){
    //      $usuario->password = Hash::make($request->password_nueva);
    //     }

    //     $usuario->save();
        
    //     return redirect()->route('posts.index', $usuario);

    // }
}
