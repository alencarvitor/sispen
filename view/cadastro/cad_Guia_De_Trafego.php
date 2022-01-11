<?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: '../../index.php');*/
      include_once('../../conexao.php');
  ?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <script type="text/javascript" src="../../script.js"></script>
</head>
<body><h1> Saída de Viatura </h1>
	<div class="itemsatuais">
  
    <form action="../../controller/processa/proc_Guia_De_Trafego.php" method="post" name="form_Guia_de_Trafego">

    	Motorista:<input type="text" name="motorista">
        CPF Motorista:<input type="text" name="documento" onKeyDown="Mascara(this,Cpf);" onKeyPress="Mascara(this,Cpf);" onKeyUp="Mascara(this,Cpf);">
        Viatura: <select name="viatura" id="viatura">
        <option value="">Escolha a Categoria</option>
        <?php
          $result_cat_post = "SELECT * FROM viatura ORDER BY placa";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);



          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['placa'].'</option>';;
          }
        ?>
      </select>
    	KM - Saída:<input type="number" name="km_Saida">
    	
    	Destino:<input type="text" name="destino">		
    		

    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
