<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $modelos = array();

        if ($request->has('atributos_marca')) {
            $atributos_marca = $request->atributos_marca;
            $modelos = $this->modelo->with('marca:id,'.$atributos_marca);
        }
        else{
            $modelos = $this->modelo->with('marca');
        }

        if ($request->has('filtro')) {
            $filtros = explode(';', $request->filtro);

            foreach ($filtros as $key => $filtro) {
                $f = explode(':',$filtro);
                $modelos = $modelos->where($f[0], $f[1], $f[2]);
            }
        }

        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $modelos = $modelos->selectRaw($atributos)->get();
        }
        else{
            $modelos = $modelos->get();
        }

        return response()->json($modelos, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Não é possível mostrar, porque o item não existe'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //TESTE: MODIFICAÇÕES REALIZADAS ATRAVÉS DE UM COMENTÁRIO, PARA RESOLVER O PROBLEMA DE NÃO PASSAR IMAGEM 

        $imagem_urn = ''; //TESTE
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return ['erro' => 'Não é possível atualizar, porque o item não existe'];
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();

            foreach ($modelo->rules() as $input => $regra) {
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas);
        }
        else{
            $request->validate($modelo->rules());
        }

        // Exclusão da imagem
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);

            //TESTE
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos','public');
            //FIM DO TESTE
        }

        /*
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public');
        */

        $modelo->fill($request->all());
        //$modelo->imagem = $imagem_urn;
        $modelo->imagem = $imagem_urn != '' ? $imagem_urn : $modelo->imagem ; //TESTE
        $modelo->save();

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return ['erro' => 'Não é possível excluir, porque o item não existe'];
        }

        // Exclusão da imagem
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi excluído com sucesso!'], 200);
    }
}
