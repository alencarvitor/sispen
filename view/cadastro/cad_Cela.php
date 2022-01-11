<?php

session_start();
include_once('../../conexao.php'); 
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
}else{
  

}
?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Cadastro de Cela</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body>
<center><h1>3° Regional Entorno de Brasília</h1></center>
	<div class="itemsatuais">
<h1>Cadastro de Celas na Unidade</h1> 
    <form action="" method="post" name="">

        Estado: <select name="fk_Estado" id="fk_Estado">
        <option value="">Estado</option>
        <?php
          $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
    </select>


        Regional:
        <select name="fk_Regional">
             <option value="">Regional</option>
        <?php
          $result_cat_post = "SELECT * FROM regional ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
        </select>

         Cidade:
        <select name="fk_Cidade">
             <option value="">Cidade</option>
        <?php
          $result_cat_post = "SELECT * FROM cidade ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
        </select>

    	Unidade Prisional:
    	   <select name="fk_Unidade">
             <option value="">Unidade</option>
        <?php
          $result_cat_post = "SELECT * FROM regional ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
        </select>
        
    	Bloco:<input type="text" name="nome">
    	Numero:<input type="text" name="numero">

    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
