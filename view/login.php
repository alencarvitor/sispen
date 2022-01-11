<?php
session_start(); 

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>Login</title>
			 <link rel="stylesheet" type="text/css" href="../../model/css.css">
    	<link rel="stylesheet" type="text/css" href="../../model/script.js">

	</head>
	<body>
		<center>

			
 		</div>
   
     
 
 <br><br><br><br>
		<h2>Área restrita</h2>
		<?php
			if(isset($_SESSION['msg'])){
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
		?>
		<form method="POST" action="../../sispen/controller/validacao/valida_Login.php">
			<label>Usuário</label>
			<input type="text" class='form-control col-3' name="usuario" placeholder="Digite o seu usuário"><br><br>
			
			<label>Senha</label>
			<input type="password" class='form-control col-3' name="senha" placeholder="Digite a sua senha"><br><br>
			
			<input type="submit" name="btnLogin" value="Acessar">
		
		</form>
		 <br>
		</center>
	</body>
</html>