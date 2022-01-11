<?php 
session_start();
include_once("../../conexao.php");

include("functions.php");
/*
   if(!isOnline())
      header(location: '../../index.php');*/
  ?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Cadastro de Viatura</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body>
	<div class="itemsatuais">
		<h1>Cadastro de Veiculo Visitante</h1>
    <form action="../../controller/processa/.php" method="post" >
    	
    	Nome Condutor:<input type="text" name="nome">
    	Documento do Condutor:<input type="text" name="documento">
    	Tipo Documento: <select name="tipo_documento" id="tipo_documento">
        <option value="">Escolha a Categoria</option>
        <?php
          $result_cat_post = "SELECT * FROM tipo_documento ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
      </select>
    	Placa:<input type="text" name="placa">
    	Cor:<input type="text" name="cor">
    	Modelo:<input type="text" name="modelo">


    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
