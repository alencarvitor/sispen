<?php
require_once "db.php";

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th><th>Nome</th><th>Descrição</th><th>Valor</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['descricao']}</td>
                <td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum produto cadastrado.";
}
?>
