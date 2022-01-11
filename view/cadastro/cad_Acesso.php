<?php
session_start();
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
}else{
  

}
?>
<!-- <?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: 'index.php');*/
  ?> -->

  <!DOCTYPE>
  <html>
  <head>
  	<title>Geren. Acesso</title>
  	<meta charset="UTF-8"/>
  	 <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
  </head>
  <body>
  <center> <h1>Gestão de Acesso a Unidade Prisional 3° Regional</h1> </center>
<div class="itemsatuais">
<form action="../../controller/processa/proc_acesso.php" method="post">
 
  Nome: <input type="text" name="nome">
  Documento de Identificação: <input type="text" name="documento">
  Tipo de Documento:
  <select name="tipo_Documento">
  	<option value="1">RG</option>
  	<option value="2">CPF</option>
  	<option value="3">OAB</option>
  	
  </select>
  Unidade Federativa:
     <select name="tipo_documento" id="tipo_documento">
        <option value=""></option>
         <?php
          $result_cat_post = "SELECT * FROM tipo_documento ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?> 
      </select>
  Tipo de Acesso:
  <select name="tipo_Acesso">
    <option value="1">VISITANTE</option>
    <option value="2">RELIGIOSO</option>
    <option value="3">OFICIAL DE JUSTIÇA</option>
    <option value="4">MOTO BOY</option>
  </select>
  <button  class="button">Registrar Acesso</button>
  </form>
</div>
  </body>
  </html>