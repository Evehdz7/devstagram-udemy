@extends('layouts.app')

@section('titulo')
    {{ $post->titulo }}
@endsection

@section('contenido')
    <div class="container mx-auto md:flex">
        <div class="md:w-1/2">
            <img src="{{ asset('uploads') . '/' . $post->imagen }}" alt="Imagen del post {{ $post->titulo}}">
            <div class="p-3 flex items-center gap-4">
                @auth 
                    <livewire:like-post :post="$post"/>
                @endauth
            </div>
            <div>
                <p class="font-bold">{{ $post->user->username }}</p>
                <p class="text-sm text-gray-500">
                    {{ $post->created_at->diffForHumans()}}
                </p>
                <p class="mt-5">
                    {{ $post->descripcion}}
                </p>
            </div>

            @auth
                @if ($post->user_id === auth()->user()->id)  
                    <form method="POST" action="{{route('posts.destroy', $post)}}">
                        @method('DELETE')
                        @csrf
                        <input 
                        type="submit" 
                        value="Eliminar Post" 
                        class="bg-red-500 hover:bg-red-600 p-2 rounded text-white font-bold mt-4 cursor-pointer">
                    </form>
                @endif
            @endauth

        </div>
        <div class="md:w-1/2">
            <div class="shadow bg-white p-5 mb-5 overflow-y-scroll">
                <div class="bg-white shadow mb-5 max-h-96 ">
                    
                    @if ($post->comentarios->count())
                        @foreach ($post->comentarios as $comentario)
                            <div class="p-5 border-gray-300 border-b">
                                <a href="{{route('posts.index', $comentario->user)}}" class="font-bold">{{$comentario->user->username}}</a>
                                <p>{{ $comentario->comentario }}</p>
                                <p class="text-sm text-gray-500">{{ $comentario->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    
                    @else
                        <p class="p-10 text-center">Sé el primer usuario en comentar</p>
                    @endif
                </div>
                
                @auth
                <p class="text-xl font-bold text-center mb-4">Agrega un comentario</p>

                @if(session('mensaje'))
                    <div class="bg-green-500 p-2 rounded-lg mb-6 text-white text-center uppercase font-bold">
                        {{session('mensaje')}}
                    </div>
                @endif
                <form action="{{route('comentarios.store', ['post' => $post, 'user' => $user])}}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label for="comentario" class="block uppercase text-gray-500 font-bold">
                            Comentario
                        </label>
                        <textarea 
                        id="comentario"
                        name="comentario"
                        placeholder="Añade comentario"
                        class="border p-3 w-full rounded-lg mb-2
                        @error('comentario')
                        border-red-500
                        @enderror">{{old('comentario')}}</textarea>
                        @error('comentario')
                        <p class="bg-red-500 text-white my-2 rounded-lg text-sm p-2 text-center">
                            {{$message}}
                        </p>
                        @enderror
                        <input 
                            type="submit" 
                            value="Comentar" 
                            class="bg-sky-600 hover:bg-sky-700 transition-colors cursor-pointer uppercase font-bold w-full p-3 text-white rounded-lg">
                    </div>
                </form>
                @endauth
            </div>
        </div>
    </div>
@endsection