<?php
session_start();

require_once('../../ViewConnection/ConnectionInventario.php');

function sanitize($conn, $input) {
    return $conn->real_escape_string($input);
}

function datasSaoValidas($dataRecebimento, $dataCadastro) {
    try {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dataRecebimentoObj = DateTime::createFromFormat('Y-m-d', $dataRecebimento, $timeZone);
        $dataCadastroObj = DateTime::createFromFormat('Y-m-d', $dataCadastro, $timeZone);
        $currentDateObj = new DateTime('now', $timeZone);

        if ($dataRecebimentoObj === false || $dataCadastroObj === false) {
            return false;
        }

        if ($dataRecebimentoObj > $currentDateObj || $dataCadastroObj > $currentDateObj) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function notaFiscalExiste($conn, $numNotaFiscal) {
    $stmt = $conn->prepare("SELECT ID FROM NOTAFISCAL WHERE NUMNOTAFISCAL = ?");
    $stmt->bind_param("s", $numNotaFiscal);
    $stmt->execute();
    $stmt->store_result();
    $rows = $stmt->num_rows;
    $stmt->close();

    return $rows > 0;
}

function processarUploadArquivo($file) {
    $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $uploadDir = '../../Uploads/';
    $fileName = basename($file['name']);
    $uploadFilePath = $uploadDir . $fileName;
    $fileType = mime_content_type($file['tmp_name']);

    if (!in_array($fileType, $allowedMimeTypes)) {
        header("Location: ../ViewFail/FailCreateTipoArquivoUploadNotaFiscal.php?erro=Tipo de arquivo não permitido. Apenas PDF, JPG e PNG são aceitos. Refaça a operação e tente novamente");
        exit();
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        header("Location: ../ViewFail/FailCreateUploadNotaFiscal.php?erro=Falha ao fazer o upload do arquivo. Refaça a operação e tente novamente");
        exit();
    }

    return $uploadFilePath;
}

function cadastrarNotaFiscal($conn, $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $grupo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $filePath, $localizacao) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        if (notaFiscalExiste($conn, $numNotaFiscal)) {
            $conn->rollback();
            header("Location: ../ViewFail/FailCreateNotaFiscalExistente.php?erro=Não foi possível realizar o cadastro. Esta nota fiscal já está cadastrada no sistema");
            exit();
        }

        if (!datasSaoValidas($dataRecebimento, $dataCadastro)) {
            $conn->rollback();
            header("Location: ../ViewFail/FailCreateDataInvalida.php?erro=A data está fora do intervalo permitido. A data deve ser igual a data atual");
            exit();
        }

        $idDatacenter = recuperarIdDatacenter($conn, $dataCenter);
        if (!$idDatacenter) {
            $idDatacenter = cadastrarNovoDatacenter($conn, $dataCenter);
            if (!$idDatacenter) {
                $conn->rollback();
                header("Location: ../ViewFail/FailCreateNovoDatacenterNotaFiscal.php?erro=Não foi possível cadastrar o DataCenter referente a nota fiscal. Refaça a operação e tente novamente");
                exit();
            }
        }

        $idMaterial = recuperarIdMaterial($conn, $material);
        if (!$idMaterial) {
            $idMaterial = cadastrarNovoMaterial($conn, $material);
        }

        $idConector = recuperarIdConector($conn, $conector);
        if (!$idConector) {
            $idConector = cadastrarNovoConector($conn, $conector);
        }

        $idMetragem = recuperarIdMetragem($conn, $metragem);
        if (!$idMetragem) {
            $idMetragem = cadastrarNovaMetragem($conn, $metragem);
        }

        $idModelo = recuperarIdModelo($conn, $modelo);
        if (!$idModelo) {
            $idModelo = cadastrarNovoModelo($conn, $modelo);
        }

        $idGrupo = recuperarIdGrupo($conn, $grupo);
        if (!$idGrupo) {
            $idGrupo = cadastrarNovoGrupo($conn, $grupo);
        }

        $idFornecedor = recuperarIdFornecedor($conn, $fornecedor);
        if (!$idFornecedor) {
            $idFornecedor = cadastrarNovoFornecedor($conn, $fornecedor);
        }

        $idLocalizacao = recuperarIdLocalizacao($conn, $localizacao);
        if (!$idLocalizacao) {
            $idLocalizacao = cadastrarNovaLocalizacao($conn, $localizacao);
        }

        // Verifica se o produto já existe
        $idProduto = recuperarIdProdutoExistente($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenter, $idGrupo, $idLocalizacao);

        if ($idProduto) {
            // Atualiza o estoque existente
            atualizarEstoqueExistente($conn, $idProduto, $quantidade);
        } else {
            // Cria um novo produto
            $idProduto = cadastrarNovoProduto($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter, $idGrupo, $idLocalizacao);
            // Insere o estoque inicial
            inserirEstoqueInicial($conn, $idProduto, $quantidade);
        }

        // Insere a nota fiscal
        $stmt = $conn->prepare("INSERT INTO NOTAFISCAL (NUMNOTAFISCAL, VALORNOTAFISCAL, MATERIAL, CONECTOR, METRAGEM, MODELO, GRUPO, QUANTIDADE, FORNECEDOR, DATARECEBIMENTO, DATACADASTRO, DATACENTER, IDPRODUTO, IDDATACENTER, FILEPATH, LOCALIZACAO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssiiss", $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $grupo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $idProduto, $idDatacenter, $filePath, $localizacao);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        header("Location: ../ViewSucess/SucessCreateNotaFiscal.php?sucesso=A nota fiscal foi cadastrada com sucesso. O estoque do produto foi iniciado ou alterado");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../ViewFail/FailCreateNotaFiscal.php?erro=Não foi possível realizar o cadastro da nota fiscal. A operação será desfeita. Tente novamente");
        exit();
    }
}

function recuperarIdDatacenter($conn, $dataCenter) {
    $stmt = $conn->prepare("SELECT IDDATACENTER FROM DATACENTER WHERE NOME = ?");
    $stmt->bind_param("s", $dataCenter);
    $stmt->execute();
    $stmt->bind_result($idDatacenter);
    $stmt->fetch();
    $stmt->close();

    return $idDatacenter;
}

function cadastrarNovoDatacenter($conn, $dataCenter) {
    $stmt = $conn->prepare("INSERT INTO DATACENTER (NOME) VALUES (?)");
    $stmt->bind_param("s", $dataCenter);
    $stmt->execute();
    $idDatacenter = $stmt->insert_id;
    $stmt->close();

    return $idDatacenter;
}

function recuperarIdMaterial($conn, $material) {
    $stmt = $conn->prepare("SELECT IDMATERIAL FROM MATERIAL WHERE MATERIAL = ?");
    $stmt->bind_param("s", $material);
    $stmt->execute();
    $stmt->bind_result($idMaterial);
    $stmt->fetch();
    $stmt->close();

    return $idMaterial;
}

function cadastrarNovoMaterial($conn, $material) {
    $stmt = $conn->prepare("INSERT INTO MATERIAL (MATERIAL) VALUES (?)");
    $stmt->bind_param("s", $material);
    $stmt->execute();
    $idMaterial = $stmt->insert_id;
    $stmt->close();

    return $idMaterial;
}

function recuperarIdConector($conn, $conector) {
    $stmt = $conn->prepare("SELECT IDCONECTOR FROM CONECTOR WHERE CONECTOR = ?");
    $stmt->bind_param("s", $conector);
    $stmt->execute();
    $stmt->bind_result($idConector);
    $stmt->fetch();
    $stmt->close();

    return $idConector;
}

function cadastrarNovoConector($conn, $conector) {
    $stmt = $conn->prepare("INSERT INTO CONECTOR (CONECTOR) VALUES (?)");
    $stmt->bind_param("s", $conector);
    $stmt->execute();
    $idConector = $stmt->insert_id;
    $stmt->close();

    return $idConector;
}

function recuperarIdMetragem($conn, $metragem) {
    $stmt = $conn->prepare("SELECT IDMETRAGEM FROM METRAGEM WHERE METRAGEM = ?");
    $stmt->bind_param("s", $metragem);
    $stmt->execute();
    $stmt->bind_result($idMetragem);
    $stmt->fetch();
    $stmt->close();

    return $idMetragem;
}

function cadastrarNovaMetragem($conn, $metragem) {
    $stmt = $conn->prepare("INSERT INTO METRAGEM (METRAGEM) VALUES (?)");
    $stmt->bind_param("s", $metragem);
    $stmt->execute();
    $idMetragem = $stmt->insert_id;
    $stmt->close();

    return $idMetragem;
}

function recuperarIdModelo($conn, $modelo) {
    $stmt = $conn->prepare("SELECT IDMODELO FROM MODELO WHERE MODELO = ?");
    $stmt->bind_param("s", $modelo);
    $stmt->execute();
    $stmt->bind_result($idModelo);
    $stmt->fetch();
    $stmt->close();

    return $idModelo;
}

function cadastrarNovoModelo($conn, $modelo) {
    $stmt = $conn->prepare("INSERT INTO MODELO (MODELO) VALUES (?)");
    $stmt->bind_param("s", $modelo);
    $stmt->execute();
    $idModelo = $stmt->insert_id;
    $stmt->close();

    return $idModelo;
}

function recuperarIdGrupo($conn, $grupo) {
    $stmt = $conn->prepare("SELECT IDGRUPO FROM GRUPO WHERE GRUPO = ?");
    $stmt->bind_param("s", $grupo);
    $stmt->execute();
    $stmt->bind_result($idGrupo);
    $stmt->fetch();
    $stmt->close();

    return $idGrupo;
}

function cadastrarNovoGrupo($conn, $grupo) {
    $stmt = $conn->prepare("INSERT INTO GRUPO (GRUPO) VALUES (?)");
    $stmt->bind_param("s", $grupo);
    $stmt->execute();
    $idGrupo = $stmt->insert_id;
    $stmt->close();

    return $idGrupo;
}

function recuperarIdFornecedor($conn, $fornecedor) {
    $stmt = $conn->prepare("SELECT IDFORNECEDOR FROM FORNECEDOR WHERE FORNECEDOR = ?");
    $stmt->bind_param("s", $fornecedor);
    $stmt->execute();
    $stmt->bind_result($idFornecedor);
    $stmt->fetch();
    $stmt->close();

    return $idFornecedor;
}

function cadastrarNovoFornecedor($conn, $fornecedor) {
    $stmt = $conn->prepare("INSERT INTO FORNECEDOR (FORNECEDOR) VALUES (?)");
    $stmt->bind_param("s", $fornecedor);
    $stmt->execute();
    $idFornecedor = $stmt->insert_id;
    $stmt->close();

    return $idFornecedor;
}

function recuperarIdLocalizacao($conn, $localizacao) {
    $stmt = $conn->prepare("SELECT IDLOCALIZACAO FROM LOCALIZACAO WHERE LOCALIZACAO = ?");
    $stmt->bind_param("s", $localizacao);
    $stmt->execute();
    $stmt->bind_result($idLocalizacao);
    $stmt->fetch();
    $stmt->close();

    return $idLocalizacao;
}

function cadastrarNovaLocalizacao($conn, $localizacao) {
    $stmt = $conn->prepare("INSERT INTO LOCALIZACAO (LOCALIZACAO) VALUES (?)");
    $stmt->bind_param("s", $localizacao);
    $stmt->execute();
    $idLocalizacao = $stmt->insert_id;
    $stmt->close();

    return $idLocalizacao;
}

function recuperarIdProdutoExistente($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenter, $idGrupo, $idLocalizacao) {
    $stmt = $conn->prepare("SELECT IDPRODUTO FROM PRODUTO WHERE IDMATERIAL = ? AND IDCONECTOR = ? AND IDMETRAGEM = ? AND IDMODELO = ? AND IDFORNECEDOR = ? AND IDDATACENTER = ? AND IDGRUPO = ? AND IDLOCALIZACAO = ?");
    $stmt->bind_param("iiiiiiii", $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $idDatacenter, $idGrupo, $idLocalizacao);
    $stmt->execute();
    $stmt->bind_result($idProduto);
    $stmt->fetch();
    $stmt->close();

    if ($idProduto === null) {
        return null;
    } else {
        return $idProduto;
    }
}

function cadastrarNovoProduto($conn, $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter, $idGrupo, $idLocalizacao) {
    $stmt = $conn->prepare("INSERT INTO PRODUTO (IDMATERIAL, IDCONECTOR, IDMETRAGEM, IDMODELO, IDFORNECEDOR, DATACADASTRO, IDDATACENTER, IDGRUPO, IDLOCALIZACAO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiisiii", $idMaterial, $idConector, $idMetragem, $idModelo, $idFornecedor, $dataCadastro, $idDatacenter, $idGrupo, $idLocalizacao);
    $stmt->execute();
    $idProduto = $stmt->insert_id;
    $stmt->close();

    return $idProduto;
}

function atualizarEstoqueExistente($conn, $idProduto, $quantidade) {
    $stmt = $conn->prepare("UPDATE ESTOQUE SET QUANTIDADE = QUANTIDADE + ? WHERE IDPRODUTO = ?");
    $stmt->bind_param("ii", $quantidade, $idProduto);
    $stmt->execute();
    $stmt->close();
}

function inserirEstoqueInicial($conn, $idProduto, $quantidade) {
    $stmt = $conn->prepare("INSERT INTO ESTOQUE (IDPRODUTO, QUANTIDADE) VALUES (?, ?)");
    $stmt->bind_param("ii", $idProduto, $quantidade);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numNotaFiscal = sanitize($conn, $_POST['NumNotaFiscal']);
    $valorNotaFiscal = sanitize($conn, $_POST['ValorNotaFiscal']);
    $material = sanitize($conn, $_POST['Material']);
    $conector = sanitize($conn, $_POST['Conector']);
    $metragem = sanitize($conn, $_POST['Metragem']);
    $modelo = sanitize($conn, $_POST['Modelo']);
    $grupo = sanitize($conn, $_POST['Grupo']);
    $quantidade = sanitize($conn, $_POST['Quantidade']);
    $fornecedor = sanitize($conn, $_POST['Fornecedor']);
    $dataRecebimento = sanitize($conn, $_POST['DataRecebimento']);
    $dataCadastro = date('Y-m-d');
    $dataCenter = sanitize($conn, $_POST['DataCenter']);
    $localizacao = sanitize($conn, $_POST['Localizacao']);

    $file = $_FILES['NotaFiscalFile'];

    $filePath = processarUploadArquivo($file);

    cadastrarNotaFiscal($conn, $numNotaFiscal, $valorNotaFiscal, $material, $conector, $metragem, $modelo, $grupo, $quantidade, $fornecedor, $dataRecebimento, $dataCadastro, $dataCenter, $filePath, $localizacao);
} else {
    header("Location: ../ViewFail/FailCreateNotaFiscal.php?erro=Requisição inválida. Refaça a operação e tente novamente");
    exit();
}
?>
