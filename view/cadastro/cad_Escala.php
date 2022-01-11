<?php 


session_start();
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
}else{
  

}



/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: '../../index.php');*/
  ?>

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>CAD - Escala</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body>
	<div class="itemsatuais">
    <h2>Cadastro de Escala</h2>
    <form action="../../controller/processa/proc_Escala.php" method="post" name="form_Cad_Escala">

    	Tipo:<input type="text" name="tipo" placeholder="EX: Plantão, Expediente, Home-Ofice">
      <small></small>
    	Carga Horaria:<input type="text" name="carga_Horaria" placeholder="EX: 40h 30h 20h">
      <small></small>
      Regime:
      <input type="text" name="regime" placeholder="EX:12x3, 24x72, 12x96 - 5x2, 6x1">
      <small></small>



    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>
