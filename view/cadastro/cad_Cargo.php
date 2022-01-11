<!-- <?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: 'index.php');
*/  ?> -->
  <!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
    <title>Cargo</title>
</head>
<body>
	<div class="itemsatuais">
    <h3>CADASTRO DE CARGOS E NÍVEL DE ACESSO</h3>
    <form action="../../controller/processa/proc_Cargo.php" method="post" name="form-cad-cargo">
    	nome:<input type="text" name="nome">
    	
    <button  class="button">Cadastrar</button>

    </form>
</div>
</body>
</html>
