<?php

namespace App\Http\Controllers;


use App\Mail\NovaTarefaMail;
use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TarefaController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $tarefas = Tarefa::where('user_id', $user_id)->paginate(10);
        return view('tarefa.index',['tarefas' => $tarefas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tarefa.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $msg ='';

        $dados = $request->all('tarefa', 'data_limite_conclusao');
        $dados['user_id'] = auth()->user()->id;

        $rules=[
            'tarefa'                 => 'required|min:3|max:20',
            'data_limite_conclusao'  => 'required|date'
        ];
        $feedbacks = [
            'required'              => 'O campo :attribute deve ser preenchido',
            'tarefa.min'            => 'O campo tarefa deve ter no mínimo 3 caracteres',
            'tarefa.max'            => 'O campo tarefa deve ter no máximo 40 caracteres',
            'data_limite_conclusao' => 'O campo deve ser do tipo data'
        ];

        $request->validate($rules, $feedbacks);

        $tarefa = Tarefa::create($dados);
        $destinatario = auth()->user()->email;
        Mail::to($destinatario)->send(new NovaTarefaMail($tarefa));
        $msg = 'Tarefa cadastrada com sucesso';

        return redirect()->route('tarefa.show', ['tarefa' => $tarefa->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefa $tarefa)
    {
        return view('tarefa.show', ['tarefa' => $tarefa]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefa $tarefa)
    {
        $user_id = auth()->user()->id;
        
        if($tarefa->user_id == $user_id) {
            return view('tarefa.edit', ['tarefa' => $tarefa]);
        }

        return view('acesso-negado');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        if(!$tarefa->user_id == auth()->user()->id) {
            return view('acesso-negado');
        }
        
        $dados = $request->all('tarefa', 'data_limite_conclusao');

        $rules=[
            'tarefa'                 => 'required|min:3|max:20',
            'data_limite_conclusao'  => 'required|date'
        ];
        $feedbacks = [
            'required'              => 'O campo :attribute deve ser preenchido',
            'tarefa.min'            => 'O campo tarefa deve ter no mínimo 3 caracteres',
            'tarefa.max'            => 'O campo tarefa deve ter no máximo 40 caracteres',
            'data_limite_conclusao' => 'O campo deve ser do tipo data'
        ];

        $request->validate($rules, $feedbacks);

        $tarefa->update($dados);
        return redirect()->route('tarefa.show', ['tarefa' => $tarefa->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefa $tarefa)
    {
        if(!$tarefa->user_id == auth()->user()->id) {
            return view('acesso-negado');
        }

        $tarefa->delete();
        return redirect()->route('tarefa.index');
    }
}
