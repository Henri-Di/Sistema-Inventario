<?php
// Iniciar sessão
session_start();
session_regenerate_id(true); // Regenera o ID da sessão para aumentar a segurança

// Configurações de segurança da sessão
ini_set('session.cookie_secure', '1'); // Apenas HTTPS
ini_set('session.cookie_httponly', '1'); // Apenas HTTP
ini_set('session.use_strict_mode', '1'); // Modo restrito de sessão
ini_set('session.cookie_samesite', 'Strict'); // Protege contra CSRF
ini_set('session.cookie_lifetime', '0'); // Cookie expira com a sessão
ini_set('display_errors', '0'); // Desativar exibição de erros em produção

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['usuarioNome']) || !isset($_SESSION['usuarioCodigoP'])) {
    // Se qualquer uma das variáveis de sessão obrigatórias não estiver definida, redireciona o usuário para a página de erro de autenticação
    header("Location: ../ViewFail/FailCreateUsuarioNaoAutenticado.php?erro=" . urlencode("O usuário não está autenticado. Realize o login novamente"));
    exit(); // Interrompe a execução do script após o redirecionamento
}

// Adiciona cabeçalhos de segurança para proteger a página contra ataques

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
// 'Content-Security-Policy' (CSP) define uma política de segurança de conteúdo que ajuda a prevenir a execução de scripts maliciosos e a proteger contra ataques como Cross-Site Scripting (XSS).
// - 'default-src 'self'': Permite que recursos (scripts, estilos, etc.) sejam carregados apenas do mesmo domínio da página.
// - 'script-src 'self'': Permite apenas scripts provenientes do mesmo domínio.
// - 'style-src 'self'': Permite apenas estilos provenientes do mesmo domínio.
// - 'img-src 'self' data:': Permite imagens do mesmo domínio e também imagens embutidas em base64 (data URIs).
// - 'font-src 'self'': Permite fontes do mesmo domínio.
// - 'connect-src 'self'': Permite conexões de dados (como AJAX) apenas para o mesmo domínio.
// - 'frame-ancestors 'none'': Impede que a página seja exibida em frames ou iframes de outros sites, prevenindo ataques de clickjacking.

header("X-Content-Type-Options: nosniff");
// 'X-Content-Type-Options' impede que o navegador "adivinhe" o tipo MIME dos arquivos. Garante que o navegador interprete o tipo de conteúdo conforme declarado pelo servidor, prevenindo ataques baseados na interpretação incorreta de tipos MIME.

header("X-Frame-Options: DENY");
// 'X-Frame-Options' impede que a página seja exibida em frames ou iframes de outros sites, ajudando a prevenir ataques de clickjacking. O valor 'DENY' bloqueia totalmente a exibição da página em frames.

header("X-XSS-Protection: 1; mode=block");
// 'X-XSS-Protection' ativa a proteção contra ataques de Cross-Site Scripting (XSS). No modo 'block', o navegador bloqueia qualquer script que pareça ser um ataque XSS, em vez de tentar escapar o código.

header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
// 'Strict-Transport-Security' (HSTS) instrui o navegador a usar apenas conexões HTTPS para o site por um período especificado. 
// - 'max-age=31536000': Define o tempo que o navegador deve lembrar da política como um ano (31536000 segundos).
// - 'includeSubDomains': Aplica a política a todos os subdomínios do domínio principal.

header("Referrer-Policy: no-referrer");
// 'Referrer-Policy' controla o envio de informações de referência ao fazer solicitações para outros sites.
// - 'no-referrer': Impede o envio de informações de referência, aumentando a privacidade do usuário.

header("Feature-Policy: vibrate 'none'; camera 'none'; microphone 'none'; geolocation 'self';");
// 'Feature-Policy' permite controlar o acesso a APIs específicas e recursos do navegador.
// - 'vibrate 'none'': Desativa o acesso à API de vibração.
// - 'camera 'none'': Desativa o acesso à câmera.
// - 'microphone 'none'': Desativa o acesso ao microfone.
// - 'geolocation 'self'': Permite o acesso à localização apenas para o mesmo domínio.


header("X-Permitted-Cross-Domain-Policies: none");
// 'X-Permitted-Cross-Domain-Policies' controla o acesso a políticas de domínio cruzado. 
// - 'none': Impede que agentes externos leiam informações da página, protegendo contra ataques onde agentes externos tentam acessar dados restritos.

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// 'Cache-Control' controla como e por quanto tempo a página deve ser armazenada em cache pelos navegadores.
// - 'no-store' e 'no-cache': Garantem que a página não seja armazenada em cache.
// - 'must-revalidate': Indica que o navegador deve revalidar a página com o servidor antes de usá-la.
// - 'max-age=0': Define o tempo máximo que a página pode ser armazenada como zero.

header("Expect-CT: max-age=86400, enforce");
// 'Expect-CT' protege contra certificados SSL inválidos ou expirados.
// - 'max-age=86400': Define o tempo de cache como 24 horas (86400 segundos).
// - 'enforce': Aplica a política de Expect-CT, exigindo que o certificado seja verificado conforme as regras.

header("Access-Control-Allow-Origin: https://example.com");
// 'Access-Control-Allow-Origin' controla quais domínios têm permissão para acessar os recursos do seu servidor.
// - 'https://example.com': Substitua pelo domínio específico que deve ter acesso. Isso ajuda a evitar o acesso não autorizado de outros domínios.

require_once('../../ViewConnection/ConnectionInventario.php'); // Inclui o arquivo que estabelece a conexão com o banco de dados

// Obter o ID do parâmetro GET e sanitizar para evitar injeção de SQL
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); // Filtra e sanitiza o parâmetro 'id' para garantir que seja um número inteiro

// Verificar se o ID não está vazio
if (!empty($id)) {
    // Verificar a conexão com o banco de dados
    if ($conn->connect_error) {
        // Se houver um erro na conexão com o banco de dados, exibe a mensagem de erro e encerra o script
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Iniciar uma transação para garantir a atomicidade das operações
    $conn->begin_transaction();

    try {
        // Construir a consulta SQL para exclusão usando prepared statements
        $sql = "DELETE FROM MODELO WHERE id = ?"; // SQL para deletar um registro da tabela MODELO
        $stmt = $conn->prepare($sql); // Prepara a consulta SQL para execução
        $stmt->bind_param("i", $id); // Faz o binding do parâmetro 'id' como inteiro para evitar injeção de SQL

        // Executar a consulta SQL e verificar se a exclusão foi bem-sucedida
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Se a execução for bem-sucedida e uma linha for afetada (deletada), faz o commit da transação
            $conn->commit();
            // Redirecionar para a página de sucesso com uma mensagem informando que o modelo foi removido com sucesso
            header("Location: ../ViewSucess/SucessCreateDeleteModelo.php?sucesso=" . urlencode("O modelo foi removida com sucesso"));
            exit(); // Interrompe a execução do script após o redirecionamento
        } else {
            // Se a execução falhar ou nenhuma linha for afetada, faz o rollback da transação
            $conn->rollback();
            // Redirecionar para a página de falha com uma mensagem de erro
            header("Location: ../ViewFail/FailCreateDeleteModelo.php?erro=" . urlencode("Não foi possível remover o modelo. Refaça a operação e tente novamente"));
            exit(); // Interrompe a execução do script após o redirecionamento
        }
    } catch (Exception $e) {
        // Se ocorrer uma exceção, faz o rollback da transação
        $conn->rollback();
        // Redirecionar para a página de falha com uma mensagem de erro genérica
        header("Location: ../ViewFail/FailCreateDeleteModelo.php?erro=" . urlencode("Não foi possível remover o modelo. Refaça a operação e tente novamente"));
        exit(); // Interrompe a execução do script após o redirecionamento
    } finally {
        // Fechar o statement para liberar recursos associados
        $stmt->close();
    }
} else {
    // Se o ID estiver vazio ou não for um número válido, redirecionar para a página de falha com uma mensagem de erro
    header("Location: ../ViewFail/FailCreateDeleteModelo.php?erro=" . urlencode("Não foi possível remover o modelo. Refaça a operação e tente novamente"));
    exit(); // Interrompe a execução do script após o redirecionamento
}

// Fechar a conexão com o banco de dados
$conn->close(); // Fecha a conexão com o banco de dados para liberar recursos
?>