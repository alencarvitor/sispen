<!-- <?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: 'index.php');
*/  ?> -->
<?php 
session_start();

if(isset($_SESSION['msg']))
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
	?>

<!DOCTYPE>
<html lang="pt-br">
	<head>
        <meta charset="utf-8">
        <title>Agendamento</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link href='css/bootstrap.min.css' rel='stylesheet' type="text"/>
        <link href='css/bootstrap-reboot.min.css' rel='stylesheet' type="text"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <style type="text/css">
        	
        	body {
					  background-image: linear-gradient(-40deg, #13F1FC, #8FBC8F);
				  }
				  div#container{
				  	background-image: linear-gradient(-40deg, #fff, #fff);
				  }
        label#label{

        	font-weight: bold;
        }
        small#subtexto{
        	font-family: color
        
        	font-weight: 600;
        }

        </style>
        <script >
            $(document).ready(function() {
                $('#id_unidade').change(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: '../sispen/envia.php',
                        type: 'POST',
                        data: $('.form').serialize(),
                        success: function(data){
                            $('.recebedados').html(data);
                        }
                    });
                    return false;
                });
            });

            
        </script>
	</head>	
<body >
	<!-- Inicio -->
    <center>
	<!-- Div Texto Oculto -->
<!-- <div class="divspoiler">
<input type="button" value="↓ Termos de Uso ↓" style="box-shadow: 0px 2px 2px #777;	 font-size: 18px; 	 cursor: pointer;	  font-family: Calibri; 	  color: #FFFFFF; 	  border: none;	   border-radius: 5px;	    font-weight: normal; l	    ine-height: 18px;	    background: url() 0 -70px repeat-x #00bf26; 	    width: 200px;	    height: 35px" title="Clique para mostrar ou Ocultar os Termo de Uso!" onclick="if (this.parentNode.nextSibling.childNodes[0].style.display != '') {
		     this.parentNode.nextSibling.childNodes[0].style.display = ''; this.value = '↑ Ocultar Termos  ↑'; }
		     else {    this.parentNode.nextSibling.childNodes[0].style.display = 'none'; this.value = '↓ Termos de Uso ↓'; }" /> -->

<!-- </div><div><div class="spoiler" style="margin-top: 5px; border: solid 1px #86BD78; background: #D9EFD2; padding: 5px 10px; font-size: 16px; display: none;">
Caro Usuário informo que este pré agendamento e somente um teste que esta sendo realizado no Centro de internação Social de Luziânia afim de facilitar e agilizar o atendimento dos advogados, não sendo obrigatório seu uso, informo ainda que os dias de visita tendem demorar mais que o previsto para atendimento dos advogados devido as movimentações realizadas internamente e que sendo possivel  realizar agendamento priorizar o fim da tarde nos dias de visita, ao realizar o agendamento por este, você está aceitando todas as condições impostas pela DGAP, e que este esta apenas para teste ficando em aberto atualizações constates para melhoramento de sua performace visual e estrutural, saliento ainda que este tem a espectativa de agilizar o atendimento aos internos, e que a previsão de atendimento a solicitações dos advogados com este atendimento tendem a ser rapidas em até 10 minutos dependendo da quantidade de agendamentos para o horário. -->
<!-- Fim div Texto Oculto -->
	</center>
</div>
</div>


	<!-- Fim -->
	<center><h5>3° CRP Entorno de Brasília</h5></center>
<br>
	<div class="row">

<div id='container' class="container">
	<div class="row"> &nbsp 
	</div>	
	<!-- Div Menu superior -->
<?php 

  if($_SESSION['cargo'] =''){
  	include_once('../model/menu2.php');

  }else{
  	include_once('../model/menu.php');
  }
	
	
	?>
   
     
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#conteudoNavbarSuportado" aria-controls="conteudoNavbarSuportado" aria-expanded="false" aria-label="Alterna navegação">
    <span class="navbar-toggler-icon"></span>
  </button>
			</center>
		</nav><br>


<h3 align="center">Agendamento de Atendimento P/ Advogados  Entorno de Brasília</h3>





	<form class="form" name="fromAgenda" method="post" action="../sispen/cad_evento.php">

<!-- Div  nome do advogado -->	
		<div class="form-row">
    	

    		<div class="col">
    		<label id="label">Nome Advogado:</label>
    			<input type="text" class="form-control " name="nomeAdvogado" <?php 
    			if(!empty($_SESSION['value_nomeAdvogado'])){
    				echo "value='".$_SESSION['value_nomeAdvogado']."'";}?>	
						 <?php
							if(!empty($_SESSION['vazio_nomeAdvogado'])){
								echo "<p style='color: #f00; '".$_SESSION['vazio_nomeAdvogado']."</p>";
								unset($_SESSION['vazio_nomeAdvogado']);
							}
						 ?>

						 <?php
							if(!empty($_SESSION['value_oab'])){
								echo "value='".$_SESSION['value_oab']."'";
								unset($_SESSION['value_oab']);
							}
						 ?>	>
						 <?php
							if(!empty($_SESSION['vazio_oab'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_oab']."</p>";
								unset($_SESSION['vazio_oab']);
							}
						 ?>
   			</div>
  		

 <!-- Div Referente a OAB -->

  		<div class="col">
			<label id="label">OAB:</label>
			<input type="text" class="form-control" name="oab" placeholder="oab Completo" 
						<?php
							if(!empty($_SESSION['value_oab'])){
								echo "value='".$_SESSION['value_oab']."'";
								unset($_SESSION['value_oab']);
							}
						 ?>	>
						 <?php
							if(!empty($_SESSION['vazio_oab'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_oab']."</p>";
								unset($_SESSION['vazio_oab']);
							}
						 ?>
		</div>
</div>

<!-- Div Conta telefonico e email -->

<div class="form-row">
			<div class="col">
				<label id="label">telefone:</label>
					<input type="text" class="form-control" name="telefone" placeholder="Informe seu telefone (61) 9.9090-9090" 
						<?php
							if(!empty($_SESSION['value_telefone'])){
								echo "value='".$_SESSION['value_telefone']."'";
								unset($_SESSION['value_telefone']);
							}
						 ?>	>
						 <?php
							if(!empty($_SESSION['vazio_telefone'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_telefone']."</p>";
								unset($_SESSION['vazio_telefone']);
							}
						 ?>
		
			</div>

<!-- Div Cela do interno -->

			<div class="col">
				<label id="label">email</label>
				 <input type="email" class="form-control" name="email" placeholder="Seu melho e-mail" 
						<?php
							if(!empty($_SESSION['value_email'])){
								echo "value='".$_SESSION['value_email']."'";
								unset($_SESSION['value_email']);
							}
						 ?>>
						 <?php
							if(!empty($_SESSION['vazio_email'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_email']."</p>";
								unset($_SESSION['vazio_email']);
							}
						 ?>
	
			</div></div>

<!-- Fim div contato -->




<!-- Div Nome do interno -->
<div class="form-row">
			<div class="col">
				<label id="label">Nome do Interno:</label>
				<input type="text" class="form-control" name="nomeInterno" placeholder="Informe Nome do Interno " 
						<?php
							if(!empty($_SESSION['value_nomeInterno'])){
								echo "value='".$_SESSION['value_nomeInterno']."'";
								unset($_SESSION['value_nomeInterno']);
							}
						 ?>	>
						 <?php
							if(!empty($_SESSION['vazio_nomeInterno'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_nomeInterno']."</p>";
								unset($_SESSION['vazio_nomeInterno']);
							}
						 ?>
				<small id='subtexto'  class="form-text text-muted">Favor informar Nome do Interno </small>
			</div>

<!-- Div Cela do interno -->

			<div class="col">
				<label id="label">Cela do Interno:</label>
					<input type="text" class="form-control" name="celaInterno" placeholder="celaInterno Completo" 
						<?php
							if(!empty($_SESSION['value_celaInterno'])){
								echo "value='".$_SESSION['value_celaInterno']."'";
								unset($_SESSION['value_celaInterno']);
							}
						 ?>	>
						 <?php
							if(!empty($_SESSION['vazio_celaInterno'])){
								echo "<p style='color: #f00; '>".$_SESSION['vazio_celaInterno']."</p>";
								unset($_SESSION['vazio_celaInterno']);
							}
						 ?>
				<small id='subtexto'  class="form-text text-muted">Favor informar Cela do Interno e Caso tenha </small>
			</div></div>
<!-- Div Data -->	
	<div class="form-row">
		<div class="col">
			<div class="form-group">
				<label id="label">data:</label>
		<input class="form-control" class="data" type="date" name="data" required="" > <br>
			</div></div>

<!-- Div Unidade Prisional -->

		<div class="col">
				<div class="form-group">
						
				<label id="label">Unidade Prisional:</label>
			
			
				<select class="form-control" name="id_unidade" id="id_unidade" required="">
					<option value="">Escolha a Unidade Prisional</option>
					<?php 
						$result_cat_post = "SELECT * FROM unidade_prisional  ORDER BY nomeUnidade";
						$resultado_cat_post = mysqli_query($conn, $result_cat_post);
						while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
							if($row_cat_post['idUnidade'] == 2 || $row_cat_post['idUnidade'] == 3){
							echo '<option value="'.$row_cat_post['idUnidade'].'">'.$row_cat_post['nomeUnidade'].'</option>';
						}}
					?>
				</select>
			</div>
		</div>
		

		
	
</div>

<div class="form-row">
	<div class="col">
		<div class="form-group">
			<div class="recebedados" class="form-control">
			</div>	

		
			
		</div>
	</div>
</div>
<div class="recebedados2"></div>
	
			
			<button class=" btn btn-primary">Cadastrar Agendamento</button>
			
</form>

</body>
</div>

</div>
</html>