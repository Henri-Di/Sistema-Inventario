  <!-- Início da sessão PHP -->
<?php session_start(); ?>

<!-- Início do documento HTML -->
<!DOCTYPE html>
<html lang="pt-br">

<!-- Cabeçalho da página -->
<head>
    <!-- Charset da página -->
    <meta charset="UTF-8">
    <!-- Compatibilidade com IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Viewport para responsividade -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Biblioteca jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Biblioteca Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Biblioteca Font Awesome -->
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Biblioteca Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Estilo CSS da página -->
    <link rel="stylesheet" href="../CSS/BlueArt.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@0,1..1000;1,1..1000&display=swap">
    <!-- Título da página -->
    <title>Sistema de Inventário - Impressão Documento de Saída</title>
    <!-- Estilos específicos para impressão -->
    <style>
        @media print {
            /* Ocultar o botão de impressão */
            button {
                display: none;
            }
            /* Ocultar elementos desnecessários */
            header, footer {
                display: none;
            }
            /* Ajustar tamanho do conteúdo */
            main {
                width: 100%;
                margin: 0;
                padding: 1cm;
            }
            table {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<!-- Fim do cabeçalho da página -->

<!-- Corpo da página -->
<body id="blue-body-documento-transferencia" style="font-family:'Sofia Sans Extra Condensed';">

    <!-- Container fluid principal -->
    <div class="container-fluid">
        <!-- Conteúdo principal -->
        <main>
            <br><br><br><br>
            <!-- Linha de título e botão -->
            <div id="blue-line-title-btn-painel" style="width:66%;margin-left:17%;">
                <p id="blue-title-btn-painel">Autorização de Saída - Transferência <i class="fa fa-retweet" id="blue-icon-btn-painel"></i></p>
            </div>
            <!-- Fim da linha de título e botão -->

            <!-- Container fluid secundário -->
            <div class="container-fluid" style="margin-top:1%;margin-left:16%;">
                <p style="text-align:left;">Confirmo a saída do produto citado abaixo, para realização de transferência entre datacenters.</p>
                <br>
                
                <!-- PHP para conexão e consulta ao banco de dados -->
                <?php
                require_once('../../ViewConnection/ConnectionInventario.php');

                // Verifica a conexão com o banco de dados
                if ($conn->connect_error) {
                    die("Falha na conexão: " . $conn->connect_error);
                }

                // Obtém o ID da transferência a partir da requisição
                $id = $_GET['id'] ?? '';

                // Prepara a consulta SQL usando prepared statements
                $sql = "SELECT 
                            T.*, 
                            DC_DESTINO.NOME AS NOME_DATACENTER_DESTINO,
                            DC_ORIGEM.NOME AS NOME_DATACENTER_ORIGEM,
                            MAT_ORIGEM.MATERIAL AS NOME_MATERIAL_ORIGEM,
                            MET_ORIGEM.METRAGEM AS METRAGEM_PRODUTO_ORIGEM,
                            MAT_DESTINO.MATERIAL AS NOME_MATERIAL_DESTINO,
                            MET_DESTINO.METRAGEM AS METRAGEM_PRODUTO_DESTINO,
                            MO.MODELO AS MODELO_PRODUTO_ORIGEM,
                            FO.FORNECEDOR AS FORNECEDOR_PRODUTO_ORIGEM,
                            G.GRUPO AS NOME_GRUPO_ORIGEM,
                            L.LOCALIZACAO AS NOME_LOCALIZACAO_ORIGEM,
                            T.QUANTIDADE,
                            T.SITUACAO
                        FROM 
                            TRANSFERENCIA T
                        JOIN 
                            PRODUTO P_ORIGEM ON T.IDPRODUTO_ORIGEM = P_ORIGEM.IDPRODUTO
                        JOIN 
                            PRODUTO P_DESTINO ON T.IDPRODUTO_DESTINO = P_DESTINO.IDPRODUTO
                        JOIN 
                            MATERIAL MAT_ORIGEM ON P_ORIGEM.IDMATERIAL = MAT_ORIGEM.IDMATERIAL
                        JOIN 
                            MATERIAL MAT_DESTINO ON P_DESTINO.IDMATERIAL = MAT_DESTINO.IDMATERIAL
                        JOIN 
                            METRAGEM MET_ORIGEM ON P_ORIGEM.IDMETRAGEM = MET_ORIGEM.IDMETRAGEM
                        JOIN 
                            METRAGEM MET_DESTINO ON P_DESTINO.IDMETRAGEM = MET_DESTINO.IDMETRAGEM
                        JOIN 
                            DATACENTER DC_DESTINO ON P_DESTINO.IDDATACENTER = DC_DESTINO.IDDATACENTER
                        JOIN 
                            DATACENTER DC_ORIGEM ON P_ORIGEM.IDDATACENTER = DC_ORIGEM.IDDATACENTER
                        JOIN 
                            MODELO MO ON P_ORIGEM.IDMODELO = MO.IDMODELO
                        JOIN 
                            FORNECEDOR FO ON P_ORIGEM.IDFORNECEDOR = FO.IDFORNECEDOR
                        JOIN 
                            GRUPO G ON P_ORIGEM.IDGRUPO = G.IDGRUPO
                        JOIN 
                            LOCALIZACAO L ON P_ORIGEM.IDLOCALIZACAO = L.IDLOCALIZACAO
                        WHERE 
                            T.ID = ?";

                if ($stmt = $conn->prepare($sql)) {
                    // Vincula os parâmetros
                    $stmt->bind_param("i", $id);
                    
                    // Executa a consulta
                    $stmt->execute();

                    // Obtém os resultados
                    $result = $stmt->get_result();

                    // Verifica se houve resultados e processa-os
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Formata a data
                            $date = strtotime($row['DATA_TRANSFERENCIA']);
                            $dateformated = date("d/m/Y", $date);
                ?>
                            <!-- Tabela de exibição dos dados -->
                            <table class="table table-bordered" id="blue-table-cadastro-auxiliar-print">
                                <tr>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Código Saída</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['ID']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Produto Origem</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_MATERIAL_ORIGEM']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Produto Destino</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_MATERIAL_DESTINO']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Metragem</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['METRAGEM_PRODUTO_DESTINO']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Modelo</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['MODELO_PRODUTO_ORIGEM']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Fornecedor</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['FORNECEDOR_PRODUTO_ORIGEM']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Grupo</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_GRUPO_ORIGEM']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Localização</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_LOCALIZACAO_ORIGEM']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Data de Emissão</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $dateformated; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Datacenter de Origem</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_DATACENTER_ORIGEM']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Datacenter de Destino</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['NOME_DATACENTER_DESTINO']; ?>" />
                                    </td>
                                    <td id="colun-blue-table">
                                        <div id="blue-title-listar">Quantidade</div>
                                        <input type="text" id="blue-input-cdst" value="<?php echo $row['QUANTIDADE']; ?>" />
                                    </td>
                                </tr>
                            </table>
                            <!-- Fim da tabela de exibição dos dados -->

                            <!-- Botão para impressão -->
                            <div style="margin-left:5%;margin-top:10%;">
                                <button class="btn btn-info" style="font-size: 16pt;width:20%;" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button>
                            </div>
                            <!-- Fim do botão para impressão -->
                <?php
                        }
                    } else {
                        echo "Nenhum resultado encontrado.";
                    }

                    // Fecha a declaração
                    $stmt->close();
                } else {
                    echo "Erro ao preparar a declaração SQL: " . $conn->error;
                }

                // Fecha a conexão com o banco de dados
                $conn->close();
                ?>
                <!-- Fim do PHP para conexão e consulta ao banco de dados -->
            </div>
            <!-- Fim do container fluid secundário -->
        </main>
        <!-- Fim do conteúdo principal -->
    </div>
    <!-- Fim do container fluid principal -->
</body>
<!-- Fim do corpo da página -->

</html>
<!-- Fim do documento HTML -->