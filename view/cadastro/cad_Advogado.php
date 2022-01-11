  <?php 
session_start();
if(isset ($_SESSION['msg'])){
echo $_SESSION['msg'];
unset($_SESSION['msg']);
}else{
  

}
include_once('../../conexao.php');
/*
include("functions.php");
   if(!isOnline())
      header(location: 'index.php');*/
  ?> 

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Cadastro de Advogado</title>
    <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <script type="text/javascript" src="../../script.js"></script>
</head>
<body>
	<center><h1 class='anuncio'>Sitema Prisional 3° Regional</h1>
	</center>
	<div class="itemsatuais">
	<h1>Cadastro de Advogado</h1>
    <form action='../../controller/processa/proc_advogado.php' method="post" name="cad_Advogado">
    	

    	Nome:<input type="text" name="nome" >
    	OAB:<input type="text" name="oab">
        UF - OAB:<select name="uf_Oab" id="uf_Oab">
        <option value=""></option>
        <?php
          $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['uf'].'</option>';}?></select>
        RG:<input type="text" name="rg">
        Orgão Expeditor: <input type="text" name="orgao_Expeditor">
        UF - RG: <select name="uf_Rg" id="uf_Rg">
        <option value=""></option>
        <?php $result_cat_post = "SELECT * FROM estado ORDER BY nome";
          $resultado_cat_post = mysqli_query($conn, $result_cat_post);
          while($row_cat_post = mysqli_fetch_assoc($resultado_cat_post) ) {
            echo '<option value="'.$row_cat_post['id'].'">'.$row_cat_post['uf'].'</option>';}?></select>
    	CPF: <input type="text" name="cpf" onKeyDown="Mascara(this,Cpf);" onKeyPress="Mascara(this,Cpf);" onKeyUp="Mascara(this,Cpf);">
    	Telefone(Preferêncial):<input type="text" name="telefone_Preferencial" onKeyDown="Mascara(this,Telefone);" onKeyPress="Mascara(this,Telefone);" onKeyUp="Mascara(this,Telefone);"  >
    	Telefone(Recado):<input type="text" name="telefone_Recado" onKeyDown="Mascara(this,Telefone);" onKeyPress="Mascara(this,Telefone);" onKeyUp="Mascara(this,Telefone);">
    	E-mail:<input type="email" name="email">
    	senha:<input type="text" name="senha">
    	<button  class="button">Cadastrar Advogado</button>

    </div>
</body>
</html>
