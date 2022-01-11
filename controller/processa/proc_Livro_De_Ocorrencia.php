<?php 
session_start();
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
}else{
  

}

include("../../conexao.php");
/*$_SESSION['id'] = 0; // servidor que está fazendo envio do livro de ocorrência.
$_SESSION['id_Regional'] = 0;
$_SESSION['id_Unidade_Prisional'] = 0;*/

/*$fk_Servidor = intval($_SESSION['id']);;*/
/*$fk_Regional = intval($_SESSION['id_Regional']);*/
/*$fk_Unidade_Prisional = intval($_SESSION['id_Unidade_Prisional']);*/
$fk_Servidor = 0;
$fk_Regional = 0;
$fk_Unidade_Prisional = 0;
$chefe_De_Plantao = $_POST['chefe_De_Plantao'];
$cpf_Chefe_De_Plantao = $_POST['cpf_Chefe_De_Plantao'];
$equipe_Plantonista = $_POST['equipe_Plantonista'];
$observacao = $_POST['observacao'];
$data_Hora = date('d/m/Y \à\s H:i:s');

var_dump($_POST);

if(isset($_FILES['arquivo'])){
    $extensao = strtolower(substr($_FILES['arquivo']['name'], -4));  //pega a extensao do arquivo

    $novo_nome = md5(time()) . $extensao;  //define o nome do arquivo

    $diretorio = "../../banco/livro/ocor-"; //define o diretorio para onde enviaremos o arquivo

    move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.$novo_nome); //efetua o upload

  	$nome_Livro_de_Ocorrencia = $novo_nome;

    $cad = "INSERT INTO livro_ocorrencia (fk_Servidor, fk_Regional, fk_Unidade_Prisional, chefe_De_Plantao, cpf_Chefe_De_Plantao, equipe_Plantonista, observacao, nome_Livro_de_Ocorrencia, created)

  VALUES( '$fk_Servidor', '$fk_Regional', '$fk_Unidade_Prisional', '$chefe_De_Plantao', '$cpf_Chefe_De_Plantao', '$equipe_Plantonista', '$observacao', '$nome_Livro_de_Ocorrencia', '$data_Hora')";
    
	$exe_cadastro = mysqli_query($conn,	$cad);

 }
  	if( mysqli_insert_id($conn)){
		header('Location: ../../../index.php');
		$_SESSION['msg'] = "<p style color:green;>Cadastro Realizado Com Sucesso!</p>";
	} else{

		header('Location: ../../view/cadastro/cad_Livro_De_Ocorrencia.php');
		$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar,
									Contate O Suporte ou verifique Suas Informações!</p>";
	}
  ?>