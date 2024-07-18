<?php
// Iniciar sessão se necessário
session_start();
session_regenerate_id(true);

// Adicionar cabeçalhos de segurança
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Obter o ID do parâmetro POST e sanitizar
$idusuario = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Verificar se o ID não está vazio e se os dados foram enviados via POST
if (!empty($idusuario) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar os valores recebidos via POST
    $nome = $conn->real_escape_string(strtoupper($_POST['Nome']));
    $codigop = $conn->real_escape_string(strtoupper($_POST['CodigoP']));
    $email = $conn->real_escape_string(strtolower($_POST['Email']));
    $datacenter = $conn->real_escape_string(strtoupper($_POST['DataCenter']));
    $nivel_acesso = $conn->real_escape_string(strtoupper($_POST['NivelAcesso']));

    // Construir a consulta SQL para atualização (usando prepared statement para segurança adicional)
    $sql = "UPDATE USUARIO SET NOME=?, CODIGOP=?, EMAIL=?, DATACENTER=?, NIVEL_ACESSO=? WHERE IDUSUARIO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $codigop, $email, $datacenter, $nivel_acesso, $idusuario);

    // Executar a consulta preparada
    if ($stmt->execute()) {
        // Redirecionar para a página de sucesso se a atualização foi bem-sucedida
        header("Location: ../ViewSucess/SucessCreateModificaUsuario.php?sucesso=" . urlencode("A alteração foi realizada com sucesso no cadastro do usuário"));
        exit(); // Termina a execução do script após o redirecionamento
    } else {
        // Redirecionar para a página de falha se houver erro na atualização
        error_log("Erro na atualização do usuário: " . $stmt->error);
        header("Location: ../ViewFail/FailCreateModificaUsuario.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do usuário. Refaça a operação e tente novamente"));
        exit(); // Termina a execução do script após o redirecionamento
    }

    // Fechar o statement
    $stmt->close();
} else {
    // Redirecionar para a página de falha se o ID estiver vazio ou se os campos necessários não foram enviados via POST
    header("Location: ../ViewFail/FailCreateModificaUsuario.php?erro=" . urlencode("Não foi possível realizar a alteração no cadastro do usuário. Refaça a operação e tente novamente"));
    exit(); // Termina a execução do script após o redirecionamento
}

// Fechar a conexão
$conn->close();
?>
