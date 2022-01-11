<?php 
/*session_start();

include("functions.php");
   if(!isOnline())
      header(location: '../../index.php');*/
      include_once("../../conexao.php");
  ?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>CAD UNIDADE PRISIONAL</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
</head>
<body>
	<div class="itemsatuais">
		<h1>Cadastro Unidade Prisional</h1>
    <form action="../../controller/processa/proc_Unidade_Priosional.php" method="post" name="form-Cad-Unidade">
    	Nome:<input type="text" name="nome">
    	
    	
    		ENDEREÇO:
    		
    	<input type="text" name="endereco">

    		Estado: <select name="id_categoria" id="id_categoria">
        <option value="">Escolha a Categoria</option>
        <?php
          $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
      </select>
    	Cidade: <select name="id_sub_categoria" id="id_sub_categoria">
        <option value="">Escolha a Cidade</option>
      </select>
      <center>INFORMAÇÕES DE CONTATO:</center><br>         
    	E-mail:<input type="email" name="email">
      Telefone:<input type="text" name="telefone">
   <button  class="button">Cadastrar</button>
    </form>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">    google.load("jquery", "1.4.2");
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
</div>
</body>
</html>
