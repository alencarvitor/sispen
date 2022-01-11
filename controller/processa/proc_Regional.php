
<?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: 'index.php');*/
      
include_once('../../conexao.php');

$nome   = $_POST['nome'];
$estado = intval($_POST['id_categoria']);
$cidade = intval($_POST['id_sub_categoria']);



$cad = "INSERT INTO regional(fk_Estado, fk_Cidade, nome ) VALUES ( '$estado', '$cidade','$nome')";
$resultado_cadastro = mysqli_query($conn, $cad);
if( mysqli_insert_id($conn)){
	header('Location: ../../../index.php');
	$_SESSION['msg'] = "<p style color:green;>Regional Cadastrada Com Sucesso!</p>";
} else{

	header('Location: ../../view/cad_Regional.php');
	$_SESSION['msg'] = "<p style color:red;>Erro Ao Tentar Cadastrar Contate O Suporte!</p>";
}

  ?>