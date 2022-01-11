<?php 
session_start();
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
}else{
  

}

/*
include("functions.php");
   if(!isOnline())
      header(location: '../../index.php');*/
  ?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body><h1 align="center">Envio de Livro de ocorrência</h1>
	<div class="itemsatuais">
		
    <form enctype="multipart/form-data" action="../../controller/processa/proc_Livro_De_Ocorrencia.php" method="post" name="form-Cad-Livro-De-Ocorrencia">
      Chefe de Plantão:
      <input type="text" required="" name="chefe_De_Plantao">
      CPF Chefe de Platão: 
      <input type="text" required="" name="cpf_Chefe_De_Plantao">
    	Livro de ocorrência: 
    	<input type="file" required="" name="arquivo">
      Equipe de Plantão:
      <textarea name="equipe_Plantonista" required="" id="equipe_Plantonista"placeholder="Informar nome e CPF de todos  separando por Vírgula( , ) e Traço               EX:                                 FULANO DA SILVA - 000.000.000-00,           CICLANO DA SILVA - 000.000.000-00,             JOAQUIM DA SILVA - 000.000.000-00," name="story"  rows="10" cols="39"></textarea>
       Observações:
      <textarea name="observacao" maxlength="500"   id="observacao"placeholder=" Informe Qualquer obserção caso seja necessaria" name="story"  rows="10" cols="39"></textarea>



    	
   <button  class="button">Cadastrar</button>
    </form>
</div>
</body>
</html>

