<?php
session_start();
if (isset($_SESSION['msg'])) {
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}else{

}
?>
<!DOCTYPE >
<html>
<head>
	<title>Cadastro de Lotação</title>
	<link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
<!--     <script src="../../model/jquery-3.4.1.js" type="text/javascript"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
</head>
<body>
	<div class="itemsatuais">
		<h3>CADASTRO DE LOTAÇÃO</h3>
<form enctype="multipart/form-data" name="form-Cad-Lotacao" action="../../controller/processa/proc_lotacao.php" method="POST">
	CPF Servidor Afetado:
	<input type="text" name="cpf_Servidor_Afetado">
	<div class="result"></div>
	Numero SEI:
	<input  type="texte" name="numero_Sei">
	Numero Portaria:
	<input type="text" name="numero_Portaria">
	Documento: 
	<input type="file" name="arquivo">

</form> 
</div>
<script>
      $("#cpf_Servidor_Afetado").keyup(function(){
        var busca = $("#cpf_Servidor_Afetado").val();
        $.post('../../controller/proc_Busca_Lotacao.php', {cpf_Servidor_Afetado: cpf_Servidor_Afetado},function(data){
          $("#result").html(data);
        });
      });
      $("#cpf_Servidor_Afetado").focusout(function(){
        $("#result").html("Pesquisar");
      })
    </script>
</body>
</html>