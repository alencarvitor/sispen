<?php
// permissions.php
function verificarPermissao($tipos_permitidos) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
    
    // Se o sistema de tipos de usuário estiver implementado
    if (isset($_SESSION['user_type'])) {
        if (!in_array($_SESSION['user_type'], $tipos_permitidos)) {
            header("Location: acesso_negado.php");
            exit();
        }
    }
    // Se não estiver implementado, permitir acesso (compatibilidade com sistema atual)
}
?>
