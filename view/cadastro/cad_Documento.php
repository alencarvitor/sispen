<?php 
session_start();
include_once('../../conexao.php'); 
if(isset($_SESSION['msg'])){
  echo $_SESSION['msg'];
  unset($_SESSION['msg']);
}else{

}

/*
include("functions.php");
   if(!isOnline())
      header(location: 'index.php');*/
  ?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Documentos</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body>
  <center><h1>3° Regional Entorno de Brasília</h1></center>
	<div class="itemsatuais">
    <h1>Cadastro de Documentos</h1>
    <form action="../../controller/processa/proc_Documento.php" method="post" name="form-Cad-Documento" enctype="multipart/form-data">
      CPF Servidor Afetado: <input type="text" placeholder="EX: Portarias, Intimações, Requerimentos." name="cpf">

      Tipo De Documento: 
      <select name="tipo_Documento" id="tipo_Documento">
        <option value="">Escolha o Tipo de Documento</option>
        <?php
          $result_cat_post = "SELECT * FROM tipo_documento ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
      </select>
      Numero do Sei: <input type="text" placeholder="CASO EXISTA INFORMAR" name="sei">
      Documento:<input type="file" name="arquivo">

    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
