<?php

namespace App\Http\Controllers;


use App\Search;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\SearchRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class SearchController extends Controller
{
    private $erro = '';

    public function index(){
        $idusuario=Auth::user()->id;
        $search = Search::where('idusuario',$idusuario)->get();
        return view('index', compact('search'));
    }

    public function excluir($id){
        $idusuario=Auth::user()->id;
        Search::find($id)->delete();
        $search = Search::where('idusuario',$idusuario)->get();
        return view('index', compact('search'));
    }

    public function search(Request $request)
    {


        if(empty($request->cnpj)){
            $this->erro = 'Dados incompletos';
        }
        else {

            if (!(Auth::check())) {
                $data = [
                    'email' => $request->email,
                    'password' => $request->password
                ];

                if (!(Auth::attempt($data))){
                    $this->erro = 'Dados de autenticação incorretos';
                }
                $ja_logado = 0;
            }
            else{
                $ja_logado = 1;
            }

            if(Auth::check()){
                $idusuario=Auth::user()->id;
                $content = http_build_query(array(
                    'num_cnpj' => $request->cnpj,
                    'botao' => 'Consultar',
                    'num_ie' => '',
                ));

                $context = stream_context_create(array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $content,
                    )
                ));

                $conteudo = file_get_contents('http://www.sintegra.es.gov.br/resultado.php', null, $context);
                $conteudo = $this->file_get_contents_utf8($conteudo);
                $campos = array(
                                'CNPJ'=>'',
                                'Inscrição Estadual'=>'',
                                'Razão Social'=>'',
                                'Logradouro'=>'',
                                'Número'=>'',
                                'Complemento'=>'',
                                'Bairro'=>'',
                                'Município'=>'',
                                'UF'=>'',
                                'CEP'=>'',
                                'Telefone'=>'',
                                'Atividade Econômica'=>'',
                                'Data de Inicio de Atividade'=>'',
                                'Situação Cadastral Vigente'=>'',
                                'Data desta Situação Cadastral'=>'',
                                'Regime de Apuração'=>'',
                                'Emitente de NFe desde'=>'',
                                'Obrigada a NF-e em'=>''
                          );

                foreach($campos as $k=>$c){
                   // $regex = '/' . $k . ':<\/td>[^>]*>[&nbsp;]+([^<]*)/i';
                    $regex = '/' . $k . '[ ]*:<\/td>[^>]*>[&nbsp;]*([^<]*)/i';
                    preg_match($regex,$conteudo,$valor);
                    if(isset($valor[1]))
                        $campos[$k]=$valor[1];
                }

                if(empty($campos['CNPJ'])){
                    $this->erro = 'CNPJ não encontrado';
                }
                else{
                    $resultado_json = json_encode($campos, JSON_UNESCAPED_UNICODE);
                    Search::create(['idusuario'=>$idusuario, 'cnpj'=> $request->cnpj, 'resultado_json'=>$resultado_json]);
                    if($ja_logado){
                        $search = Search::where('idusuario',Auth::user()->id)->get();
                        return view('index', compact('search'));
                    }
                    else{
                        return $resultado_json;
                    }
                }

            }

        }
        if(Auth::check()){
            $search = Search::where('idusuario',Auth::user()->id)->get();
            $errors = $this->erro;
            return view('index', compact('search','errors'));
        }
        else{
            $resposta = response()->json($this->erro,404);
            $resposta->header('Content-Type', 'application/json');
            $resposta->header('charset', 'utf-8');
            return $resposta;
        }

    }

    public function file_get_contents_utf8($content) {
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }

}
