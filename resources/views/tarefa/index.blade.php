@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tarefas<a class="float-right" href="{{ route('tarefa.create') }}" title="Criar uma nova Tarefa"><i class="fa-solid fa-plus"></i></a></div>
                <div class="card-body"> 
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Tarefa</th>
                                <th scope="col">Data limite de conclusão</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($tarefas as $key => $t)
                                <tr>
                                    <th scope="row">{{ $t['id'] }}</th>
                                    <td>{{ $t['tarefa'] }}</td>
                                    <td>{{ date('d/m/Y', strtotime($t['data_limite_conclusao'])) }}</td>
                                    <td>
                                        <a class="btn btn-primary icons-edit" href="{{ route('tarefa.edit', $t['id'] )}}"><i class="fa-solid fa-pen"></i></a>
                                    </td>
                                    <td>
                                        <form id="form_{{ $t['id'] }}" method="POST" action="{{ route('tarefa.destroy', ['tarefa' => $t['id']]) }}">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn btn-danger" type="button" data-toggle="modal"  data-target="#modalRemover"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <nav class="row d-flex justify-content-center">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="{{ $tarefas->previousPageUrl() }}">Voltar</a></li>
                            @for ($i = 1; $i <= $tarefas->lastPage(); $i++)
                                <li class="page-item {{ $tarefas->currentPage() == $i ? ' active' : '' }}">
                                    <a class="page-link" href="{{ $tarefas->url($i) }}">{{ $i }}</a>
                                </li> 
                            @endfor
                            <li class="page-item"><a class="page-link" href="{{ $tarefas->nextPageUrl() }}">Avançar</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modalRemover">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Exclusão de Tarefa</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Tem certeza que deseja exluir a {{ $t['tarefa'] }}?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
          <button type="button" class="btn btn-success" onclick="document.getElementById('form_{{ $t['id']}}').submit()">Sim</button>
        </div>
      </div>
    </div>
  </div>
@endsection
