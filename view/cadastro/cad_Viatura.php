<?php 
session_start();
include_once("../../conexao.php");
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
unset($_SESSION['msg']);
}else{
  

}

/*include("functions.php");
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
    <h1>Cadastro de Viatura</h1>
    <form action="../../controller/processa/proc_Viatura.php" method="post" name="">
        Regional <select name="tipo_documento" id="tipo_documento">
        <option value="">Escolha a Categoria</option>
        <?php
          $result_cat_post = "SELECT * FROM tipo_documento ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
      </select>
      Unidade Prisional: <select name="tipo_documento" id="tipo_documento">
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
     
      Envelopa: <br>
      <table border="1">
      <tr>
        <TD> SIM <input type="radio" name="SIM" id=""> </TD>
     
       <TD>NÃO <input type="radio" name="nao"></TD></tr> </table>
    	
      Modelo:<input type="text" name="modelo">
    	Ano:<input type="date" name="ano">
    	Renavam:<input type="number" name="renavam">
    	Chassi:<input type="text" name="chassi">
    	Hodômetro:<input type="number" name="hodometro">


    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
