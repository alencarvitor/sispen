<?php 
session_start();
include_once('../../conexao.php');

//ENcapsulamento de dados do Formulario

$fk_Servidor  = 0;
$fk_Reginal_Servidor  = 0;
$fk_Lotacao_Servidor  = 0;
$fk_Escala_Servidor  = 0;
$fk_Cargo_Servidor  = $_POST['cargo'];
$nome_Servidor  = $_POST['nome'];
$rg_Servidor  = $_POST['rg'];
$orgao_Expeditor_Servidor  = $_POST['orgao'];
$uf_Rg_Servidor  = $_POST['estado'];
$cpf_Servidor  = preg_replace('/[^0-9]/', '', $_POST['cpf']);;
$estado_Civil_Servidor  = $_POST['estado_Civil_Servidor'];
$sexo_Servidor  = $_POST['sexo'];
$formacao_Acad_Servidor  = $_POST['escolaridade'];
$telefone_Pref__Servidor  = $_POST['telefone_Preferencial'];
$telefone_Rec_Servidor  = $_POST['telefone_Recado'];
$email_Servidor  = $_POST['email'];
$senha_Servidor  = $_POST['senha'];
$endereco_Servidor  = $_POST['endereco'];
$cep_Servidor  = $_POST['cep'];
$estado_Servidor  = $_POST['id_categoria'];
$cidade_Servidor = $_POST['id_sub_categoria'];
var_dump($_POST);
//Fim  Encapsulamento de formulario
//encapsulamento de dados para sessão caso haja erro de cadastro 
	
$_SESSION['fk_Cargo_Servidor'] = $fk_Cargo_Servidor;
$_SESSION['nome_Servidor'] = $nome_Servidor;
$_SESSION['rg_Servidor'] = $rg_Servidor;
$_SESSION['orgao_Expeditor_Servidor'] = $orgao_Expeditor_Servidor;
$_SESSION['uf_Rg_Servidor'] = $uf_Rg_Servidor;
$_SESSION['cpf_Servidor'] = $cpf_Servidor;
$_SESSION['estado_Civil_Servidor'] = $estado_Civil_Servidor;
$_SESSION['sexo_Servidor'] = $sexo_Servidor;
$_SESSION['formacao_Acad_Servidor'] = $formacao_Acad_Servidor;
$_SESSION['telefone_Pref__Servidor'] = $telefone_Pref__Servidor;
$_SESSION['telefone_Rec_Servidor'] = $telefone_Rec_Servidor;
$_SESSION['email_Servidor'] = $email_Servidor;
$_SESSION['senha_Servidor'] = $senha_Servidor;
$_SESSION['endereco_Servidor'] = $endereco_Servidor;
$_SESSION['cep_Servidor'] = $cep_Servidor;
$_SESSION['estado_Servidor'] = $estado_Servidor;
$_SESSION['cidade_Servidor'] = $cidade_Servidor;

//Fim  Encapsulamento de sessao				 

// conversão de senha para criptografia.
$senha_Servidor = md5($senha_Servidor);
//fim criptografia.

//inicio da inserção de dados no banco.

	$cad = "INSERT INTO servidor (
fk_Servidor ,
fk_Reginal_Servidor ,
fk_Lotacao_Servidor ,
fk_Escala_Servidor ,
fk_Cargo_Servidor ,
nome_Servidor ,
rg_Servidor ,
orgao_Expeditor_Servidor ,
uf_Rg_Servidor ,
cpf_Servidor ,
estado_Civil_Servidor ,
sexo_Servidor ,
formacao_Acad_Servidor ,
telefone_Pref__Servidor ,
telefone_Rec_Servidor ,
email_Servidor ,
senha_Servidor ,
endereco_Servidor ,
cep_Servidor ,
estado_Servidor ,
cidade_Servidor)

VALUES( '$fk_Servidor',
		'$fk_Reginal_Servidor',
		'$fk_Lotacao_Servidor',
		'$fk_Escala_Servidor',
		'$fk_Cargo_Servidor',
		'$nome_Servidor',
		'$rg_Servidor',
		'$orgao_Expeditor_Servidor',
		'$uf_Rg_Servidor',
		'$cpf_Servidor',
		'$estado_Civil_Servidor',
		'$sexo_Servidor',
		'$formacao_Acad_Servidor',
		'$telefone_Pref__Servidor',
		'$telefone_Rec_Servidor',
		'$email_Servidor',
		'$senha_Servidor',
		'$endereco_Servidor',
		'$cep_Servidor',
		'$estado_Servidor',
		'$cidade_Servidor')";

	$exe_cadastro = mysqli_query($conn, $cad);
//fim da execução query de inserção de dados
	//inicio da validação de inserção9
	if( mysqli_insert_id($conn)){
        unset($_SESSION['fk_Cargo_Servidor']);
        unset($_SESSION['nome_Servidor']);
        unset($_SESSION['rg_Servidor']);
        unset($_SESSION['orgao_Expeditor_Servidor']);
        unset($_SESSION['uf_Rg_Servidor']);
        unset($_SESSION['cpf_Servidor']);
        unset($_SESSION['estado_Civil_Servidor']);
        unset($_SESSION['sexo_Servidor']);
        unset($_SESSION['formacao_Acad_Servidor']);
        unset($_SESSION['telefone_Pref__Servidor']);
        unset($_SESSION['telefone_Rec_Servidor']);
        unset($_SESSION['email_Servidor']);
        unset($_SESSION['senha_Servidor']);
        unset($_SESSION['endereco_Servidor']);
        unset($_SESSION['cep_Servidor']);
        unset($_SESSION['estado_Servidor']);
        unset($_SESSION['cidade_Servidor']);
		header('Location: ../../../index.php');
		
		$_SESSION['msg'] = "<p style color:green;>Servidor Cadastrado com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Servidor.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar Servidor, Contate O Suporte ou verifique as informações Fornecidas!</p>";
	}
