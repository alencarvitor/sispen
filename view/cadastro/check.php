<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>Celke - Popula o select conforme escolha anterior</title>		
		<style type="text/css">
			.carregando{
				color:#ff0000;
				display:none;
			}
		</style>
	</head>
	<body>
		<h1>Cadastrar Artigo</h1>
		<?php include_once("../../conexao.php"); ?>
		
		<form method="POST" action="salvar_post.php">
			<label>Titulo:</label>
			<input type="text" name="titulo" placeholder="Titulo do Artigo"> <br><br>
			
			<label>Categoria:</label>
			<select name="id_categoria" id="id_categoria">
				<option value="">Escolha a Categoria</option>
				<?php
					$result_cat_post = "SELECT * FROM estado ORDER BY nome";
					$resultado_cat_post = mysqli_query($conn, $result_cat_post);
					while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
						echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';
					}
				?>
			</select><br><br>
			
			<label>Subcategoria:</label>
			<span class="carregando">Aguarde, carregando...</span>
			<select name="id_sub_categoria" id="id_sub_categoria">
				<option value="">Escolha a Subcategoria</option>
			</select><br><br>
			
			<input type="submit" value="Cadastrar">
		</form>
		
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		  google.load("jquery", "1.4.2");
		</script>
		
		<script type="text/javascript">
		$(function(){
			$('#id_categoria').change(function(){
				if( $(this).val() ) {
					$('#id_sub_categoria').hide();
					$('.carregando').show();
					$.getJSON('sub_categorias_post.php?search=',{id_categoria: $(this).val(), ajax: 'true'}, function(j){
						var options = '<option value="">Escolha Subcategoria</option>';	
						for (var i = 0; i < j.length; i++) {
							options += '<option value="' + j[i].id + '">' + j[i].nome_sub_categoria + '</option>';
						}	
						$('#id_sub_categoria').html(options).show();
						$('.carregando').hide();
					});
				} else {
					$('#id_sub_categoria').html('<option value="">– Escolha Subcategoria –</option>');
				}
			});
		});
		</script>
		
	</body>
</html>