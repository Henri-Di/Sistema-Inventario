<?php
// Iniciar sessão
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Verificar se o formulário foi submetido via método POST e se todos os campos estão presentes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['NomeUsuario'], $_POST['CodigoUsuario'], $_POST['SenhaUsuario'], $_POST['EmailUsuario'], $_POST['DataCenter'], $_POST['NiveldeAcesso'])) {
    // Receber os dados do formulário
    $nome = strtoupper(filter_input(INPUT_POST, 'NomeUsuario', FILTER_SANITIZE_SPECIAL_CHARS));
    $codigo = strtoupper(filter_input(INPUT_POST, 'CodigoUsuario', FILTER_SANITIZE_SPECIAL_CHARS));
    $senha = password_hash($_POST['SenhaUsuario'], PASSWORD_DEFAULT); // Hasheando a senha
    $email = filter_input(INPUT_POST, 'EmailUsuario', FILTER_SANITIZE_EMAIL);
    $datacenter = strtoupper(filter_input(INPUT_POST, 'DataCenter', FILTER_SANITIZE_SPECIAL_CHARS));
    $nivel_acesso = filter_input(INPUT_POST, 'NiveldeAcesso', FILTER_SANITIZE_NUMBER_INT);

    // Verificar se o usuário ou e-mail já existem no banco de dados
    $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE CODIGOP = ? OR EMAIL = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $codigo, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuário ou e-mail já existem
            $_SESSION['message'] = "Usuário ou e-mail já existem.";
            header("Location: ../ViewFail/FailCreateUsuarioExistente.php?erro=Não foi possível realizar o cadastro do novo usuário. Informações de um usuário já existente estão sendo utilizadas");
            exit(); // Termina a execução do script após redirecionamento
        } else {
            // Inserir o novo usuário no banco de dados com PRIMEIRO_LOGIN = 1
            $stmt = $conn->prepare("INSERT INTO USUARIO (NOME, CODIGOP, SENHA, EMAIL, DATACENTER, NIVEL_ACESSO, PRIMEIRO_LOGIN) VALUES (?, ?, ?, ?, ?, ?, 1)");
            if ($stmt) {
                $stmt->bind_param("sssssi", $nome, $codigo, $senha, $email, $datacenter, $nivel_acesso);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Usuário criado com sucesso.";
                    header("Location: ../ViewSucess/SucessCreateUsuario.php?sucesso=O cadastro do usuário foi realizado com sucesso");
                    exit(); // Termina a execução do script após redirecionamento
                } else {
                    $_SESSION['message'] = "Falha ao criar usuário.";
                    header("Location: ../ViewFail/FailCreateNovoUsuario.php?erro=Não foi possível realizar o cadastro do usuário. Refaça a operação e tente novamente");
                    exit(); // Termina a execução do script após redirecionamento
                }
            } else {
                $_SESSION['message'] = "Erro na preparação da inserção de usuário.";
                header("Location: ../ViewFail/FailCreateInserirDadosUsuario.php?erro=Não foi possível inserir os dados na tabela USUARIO. Informe o departamento de TI");
                exit(); // Termina a execução do script após redirecionamento
            }
        }
    } else {
        $_SESSION['message'] = "Erro na preparação da consulta.";
        header("Location: ../ViewFail/FailCreateNovoUsuario.php?erro=Não foi possível realizar o cadastro do usuário. Refaça a operação e tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Fechar a conexão e o statement
    $stmt->close();
    $conn->close();
}
?>
