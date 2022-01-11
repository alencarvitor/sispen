
<?php

session_start();
include_once('../../conexao.php');

$busca =  preg_replace('/[^0-9]/', '', $_POST['busca']); 


$query = mysqli_query($conn, "SELECT * FROM servidor WHERE cpf_Servidor LIKE '%$busca%' LIMIT 5");
$num   = mysqli_num_rows($query);
if($num >0){
    while($row = mysqli_fetch_assoc($query)){
echo ''.$row['nome_Servidor'].' - '. $row['cpf_Servidor']. '<input name="id_Servidor" type="checkbox" value="'.$row['id'].'"><br><hr>';

   
      

    }
}else{
  echo "Esta Pessoa Não Existe!";
}
?>