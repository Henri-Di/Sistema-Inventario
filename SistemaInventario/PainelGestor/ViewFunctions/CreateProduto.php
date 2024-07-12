<?php
session_start();

// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Função para sanitizar entrada de dados
function sanitize($conn, $input) {
    return $conn->real_escape_string($input);
}

// Função para validar se as datas de recebimento e cadastro são válidas
function datasSaoValidas($datacadastro) {
    try {
        // Definir a zona de tempo para as datas recebidas e a data atual do servidor
        $timeZone = new DateTimeZone('America/Sao_Paulo'); // Substitua pela sua zona de tempo

        // Converter as datas para objetos DateTime com a zona de tempo definida
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $datacadastro, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone); // Data atual do servidor

        // Comparar apenas a parte da data (sem considerar horas, minutos, segundos)
        $dataCadastroFormatada = $dataCadastroObj->format('Y-m-d');
        $currentDate = $currentDateObj->format('Y-m-d');

        // Verificar se as datas recebidas são iguais à data atual do servidor
        if ($dataCadastroFormatada !== $currentDate) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        // Tratar exceção de conversão de datas
        return false;
    }
}

// Função para obter ou inserir e obter o ID de uma tabela
function getIdOrInsert($conn, $table, $column, $idColumn, $value) {
    $checkSql = "SELECT $idColumn FROM $table WHERE UPPER($column) = UPPER('$value')";
    $result = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row[$idColumn];
    } else {
        $insertSql = "INSERT INTO $table ($column) VALUES ('$value')";
        if (mysqli_query($conn, $insertSql)) {
            return $conn->insert_id;
        } else {
            header("Location: ../ViewFail/FailCreateInserirDadosTabela.php?erro=Não foi possível inserir dados nas tabelas do banco de dados. Informe o departamento de TI ");
            exit(); // Termina a execução do script após redirecionamento
        }
    }
}

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário e sanitizar
    $material = sanitize($conn, $_POST['Material'] ?? '');
    $conector = sanitize($conn, $_POST['Conector'] ?? '');
    $metragem = sanitize($conn, $_POST['Metragem'] ?? '');
    $modelo = sanitize($conn, $_POST['Modelo'] ?? '');
    $quantidade = sanitize($conn, $_POST['Quantidade'] ?? '');
    $fornecedor = sanitize($conn, $_POST['Fornecedor'] ?? '');
    $datacadastro = sanitize($conn, $_POST['DataCadastro'] ?? '');
    $datacenterNome = sanitize($conn, $_POST['DataCenter'] ?? '');
    $grupo = sanitize($conn, $_POST['Grupo'] ?? '');
    $localizacao = sanitize($conn, $_POST['Localizacao'] ?? '');

    // Obter o ID do usuário a partir da sessão
    $idUsuario = $_SESSION['usuarioId'] ?? '';

    // Sanitizar o ID do usuário para evitar injeção de SQL
    $idUsuario = $conn->real_escape_string($idUsuario);

    // Consulta para obter o datacenter e o nível de acesso do usuário
    $consultaDatacenterNivelAcesso = "SELECT UPPER(DATACENTER), NIVEL_ACESSO FROM USUARIO WHERE IDUSUARIO = ?";
    if ($stmt = $conn->prepare($consultaDatacenterNivelAcesso)) {
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->bind_result($datacenterUsuario, $nivelAcesso);
        $stmt->fetch();
        $stmt->close();
    }

    // Verificar se a quantidade é negativa
    if ($quantidade < 0) {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateQuantidadeNegativa.php?erro=Não é permitido o registro de valores negativos no campo de quantidade");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se a data de cadastro é válida
    if (!datasSaoValidas($datacadastro)) {
        // Redirecionar para a página de falha com mensagem de erro
        header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido. A data deve ser igual a data atual");
        exit(); // Termina a execução do script após redirecionamento
    }

    // Verificar se o datacenter do usuário é igual ao datacenter recebido pelo formulário, exceto se o nível de acesso for "GESTOR"
    if (strtoupper($nivelAcesso) !== 'GESTOR' && strtoupper($datacenterUsuario) !== strtoupper($datacenterNome)) {
        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateProdutoDatacenterIncorreto.php?erro=Você não pode cadastrar um produto que seja de outro datacenter");
        exit(); // Termina a execução do script após redirecionamento
    }

    try {
        $conn->begin_transaction();

        $idMaterial = getIdOrInsert($conn, 'MATERIAL', 'MATERIAL', 'IDMATERIAL', strtoupper($material));
        $idConector = getIdOrInsert($conn, 'CONECTOR', 'CONECTOR', 'IDCONECTOR', strtoupper($conector));
        $idMetragem = getIdOrInsert($conn, 'METRAGEM', 'METRAGEM', 'IDMETRAGEM', strtoupper($metragem));
        $idModelo = getIdOrInsert($conn, 'MODELO', 'MODELO', 'IDMODELO', strtoupper($modelo));
        $idFornecedor = getIdOrInsert($conn, 'FORNECEDOR', 'FORNECEDOR', 'IDFORNECEDOR', strtoupper($fornecedor));
        $idDataCenter = getIdOrInsert($conn, 'DATACENTER', 'NOME', 'IDDATACENTER', strtoupper($datacenterNome));
        $idGrupo = getIdOrInsert($conn, 'GRUPO', 'GRUPO', 'IDGRUPO', strtoupper($grupo));
        $idLocalizacao = getIdOrInsert($conn, 'LOCALIZACAO', 'LOCALIZACAO', 'IDLOCALIZACAO', strtoupper($localizacao));

        // Verificar se um produto com os mesmos detalhes, exceto fornecedor e modelo, já existe no mesmo datacenter
        $check_sql = "SELECT p.* FROM PRODUTO p
                      WHERE p.IDMATERIAL = '$idMaterial'
                      AND p.IDCONECTOR = '$idConector'
                      AND p.IDMETRAGEM = '$idMetragem'
                      AND p.IDDATACENTER = '$idDataCenter'
                      AND p.IDGRUPO = '$idGrupo'
                      AND p.IDLOCALIZACAO = '$idLocalizacao'
                      AND p.IDFORNECEDOR = '$idFornecedor'
                      AND p.IDMODELO = '$idModelo'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            // Se o produto já existe com os mesmos detalhes (inclusive fornecedor e modelo), redirecionar para a página de falha
            header("Location: ../ViewFail/FailCreateProdutoExistente.php?erro=Não foi possível realizar o cadastro. Produto já cadastrado");
            exit(); // Termina a execução do script após redirecionamento
        } else {
            // Inserir dados na tabela PRODUTO
            $sqlInsertProduto = "INSERT INTO PRODUTO (IDMATERIAL, IDCONECTOR, IDMETRAGEM, IDMODELO, IDFORNECEDOR, DATACADASTRO, IDDATACENTER, IDGRUPO, IDLOCALIZACAO) 
                                 VALUES ('$idMaterial', '$idConector', '$idMetragem', '$idModelo', '$idFornecedor', '$datacadastro', '$idDataCenter', '$idGrupo', '$idLocalizacao')";
            if (!mysqli_query($conn, $sqlInsertProduto)) {
                header("Location: ../ViewFail/FailCreateInserirDadosProduto.php?erro=Não foi possível inserir os dados na tabela PRODUTO");
                exit();
            }

            // Obter o ID do produto inserido
            $idProduto = $conn->insert_id;

            // Inserir dados na tabela ESTOQUE
            $sqlInsertEstoque = "INSERT INTO ESTOQUE (IDPRODUTO, QUANTIDADE) 
                                 VALUES ('$idProduto', '$quantidade')";
            if (!mysqli_query($conn, $sqlInsertEstoque)) {
                header("Location: ../ViewFail/FailCreateInserirDadosEstoque.php?erro=Não foi possível inserir os dados na tabela ESTOQUE");
                exit();
            }

            // Commit da transação se todas as operações foram bem-sucedidas
            $conn->commit();

            // Redirecionar para a página de sucesso
            header("Location: ../ViewSucess/SucessCreateProduto.php?sucesso=O cadastro do produto foi realizado com sucesso");
            exit(); // Termina a execução do script após redirecionamento
        }
    } catch (Exception $e) {
        // Em caso de erro, fazer rollback da transação
        $conn->rollback();

        // Exibir mensagem de erro
        echo "Erro: " . $e->getMessage();

        // Redirecionar para a página de falha
        header("Location: ../ViewFail/FailCreateProduto.php?erro=Não foi possível realizar o cadastro do produto. Tente novamente");
        exit(); // Termina a execução do script após redirecionamento
    }
}

// Fechar a conexão
$conn->close();
?>
