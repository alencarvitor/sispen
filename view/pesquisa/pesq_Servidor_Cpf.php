<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    
    <title>Sistema de Busca</title>
  <link rel="stylesheet" type="text/css" href="../../model/css.css">
    <link rel="stylesheet" type="text/css" href="../../model/script.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  </head>
  <body>
  
    
         <div class="itemsatuais">
        <h3 class="text-muted">Sistema De Busca</h3>
    
    
     
          <form action="../../view/cadastro/cad_Lotacao.php" method="POST" name='form-Pesquisa-Servidor'>

            <input id="busca" type="text" >
            <button>Proximo</button>
          <p id="result"></p>
          </form>
        </div>
    <script>
      $("#busca").keyup(function(){
        var busca = $("#busca").val();
        

        $.post('../../controller/processa_Pesq/proc_Pesq_Busca_Servidor.php', {busca: busca},function(data){
          $("#result").html(data);
          e.preventDefault();
        });
      });
    
    </script>
    
  </body>
</html>