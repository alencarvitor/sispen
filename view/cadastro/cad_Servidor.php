<?php 
session_start();
include_once('../../conexao.php');
if(isset ($_SESSION['msg'])){
  echo $_SESSION['msg'];
  unset($_SESSION['msg']);
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
    <title>Cadastro de Servidor</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <script type="text/javascript" src="../../script.js"></script>
    
     
</head>
<body>
	<div class="itemsatuais">
		<h1>Cadastro de Servidor</h1>
    <form action="../../controller/processa/proc_Servidor.php" method="post" name="servidor">

Nome:<input type="text" name="nome">
RG:<input type="text" name="rg">
Orgão Expeditor:<input type="text" name="orgao">
UF:  <select name="estado" id="estado">
        <option value="">Escolha o  Estado</option>
        <?php
          $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['uf'].'</option>';
          }
        ?>
      </select>
CPF:<input type="text" name="cpf" >

Estado Civil:<select name="estado_Civil_Servidor" id="estado_Civil_Servidor">
        <option value=""></option>
        <?php
          $result_cat_post = "SELECT * FROM estado_civil ORDER BY estado_Civil";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['estado_Civil'].'</option>';
          }
        ?>
      </select>
Sexo:<select name="sexo" id="sexo">
    <option></option>
    <option value="M">MASCULINO</option>
    <option value="F">FEMININO</option>
    </select>

Escolaridade:
<select name="escolaridade" id="escolaridade">
        <option value=""></option>
        <?php
          $result_cat_post = "SELECT * FROM escolaridade ORDER BY nivel";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nivel'].'</option>';
          }
        ?>
      </select>




Telefone(Preferêncial):<input type="text" id="telefone_Preferencial" name="telefone_Preferencial">
Telefone(recado):<input type="text" name="telefone_Recado" id="telefone_Recado" placeholder="Ex.: (00) 0000-0000">

E-mail:<input type="email" name="email">
Senha:<input type="password" name="senha">


Cargo: <select name="cargo" id="cargo">
        <option value="">Escolha a Categoria</option>
        <?php
          $result_cat_post = "SELECT * FROM cargo ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['nome'].'</option>';;
          }
        ?>
      </select>

Endereco:
<input type="text" name="endereco">
CEP: <input type="text" name="cep">
Estado: <select name="id_categoria" id="id_categoria">
        <option value="">Informe Seu Estado</option>
        <?php
          $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['uf'].'</option>';;
          }
        ?>
      </select>
      Cidade: <select name="id_sub_categoria" id="id_sub_categoria">
        <option value="">Escolha a Sua Cidade</option>
      </select>
    	
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
