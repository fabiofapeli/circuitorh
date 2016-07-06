@extends('template')

@section('content')
    <div class="container-fluid">
        <div class="row">


            {!! Form::open(['method'=>'post','route'=>'search']) !!}

            <div class="form-group">
                {!! Form::label('cnpj','CNPJ:') !!}
                {!! Form::text('cnpj',null,['class'=>'form-control']) !!}
            </div>

            {!! Form::submit('Search',['class'=>'btn btn-primary']) !!}

            {!! Form::close() !!}

        </div>
        <div class="row">

            <div class="container">
                <h1>Searches</h1>
                <table class="table">
                    <tr>
                        <th>Id</th>
                        <th>Resultado</th>
                        <th></th>
                    </tr>
                    @foreach($search as $resultado)
                        <tr>
                            <td>{{$resultado->id}}</td>
                            <td>{{$resultado->resultado_json}}</td>
                            <td><a href="{{ route('excluir',['id'=>$resultado->id]) }}">excluir</a></td>
                        </tr>
                    @endforeach

                </table>
            </div>

        </div>
    </div>
@endsection