    <!-- Start session usuário -->
    <?php session_start();?>
    


    <!-- Start JavaScript validação formulario -->
    <script>
    


    // Disable form submissions if there are invalid fields
    (function() {
    
    
    'use strict';
    


    window.addEventListener('load', function() {
    


    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    


    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
    


    form.addEventListener('submit', function(event) {
    


    if (form.checkValidity() === false) {
    


    event.preventDefault();
    


    event.stopPropagation();
    


    }
    
    
    form.classList.add('was-validated');

    

    }, false);

    

    });

    

    }, false);

    

    })();
    


    </script>
    <!-- End JavaScript validação formulario -->



    <!-- Start Javascript loader page -->
    <script>
    


    var myVar;



    function myFunction() {
    


    myVar = setTimeout(showPage, 500);
    


    }



    function showPage() {
    


    document.getElementById("loader").style.display = "none";
    


    document.getElementById("blue-animate").style.display = "block";
    


    }
    


    </script>
    <!-- End Javascript loader page -->



    <!-- Start Javascript loader body -->
    <script>
    


    document.oncontextmenu = function(){return false;}
    


    </script> 
    <!-- End Javascript loader body -->



    <!-- Start document HTML/CSS/PHP/JAVASCRIPT -->
    <!DOCTYPE html>
    


    <html lang="PT-BR">



    <!-- Start top page-->
    <head>
    


    <!-- Charset page -->  
	<meta charset="UTF-8">



    <!-- Compatible IE-edge -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">



    <!-- Viewport responsive page -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    


	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	
    

    <!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	
    

    <!-- Font-awesome library -->
	<script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Bootstrap library -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    


    <!-- CSS document page -->
    <link rel="stylesheet" href="../CSS/BlueArt.css">



    <!-- Google fonts library -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    


    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@0,1..1000;1,1..1000&display=swap">



	<!-- Title page -->
    <title>Sistema de Inventário - Resultado da Pesquisa</title>	
    


    <!-- Start CSS container sidenav -->
    <style>
    


    /* Set height of the grid so .sidenav can be 100% (adjust as needed)*/
    .row.content {height: 550px}
    


    /* Set gray background color and 100% height */
    .sidenav {
    


    background-color: #ffffff;
    


    height: auto;
    


    }



    /* On small screens, set height to 'auto' for the grid */
    @media screen and (max-width: 767px) {
    


    .row.content {height: auto;} 
    


    }
    
    
    </style>
    <!-- End CSS container sidenav -->
    


    </head>
    <!-- End top page-->



    <!-- Start body page -->
    <body oncontextmenu="return false" onload="myFunction()" style="margin:0;font-family:'Sofia Sans Extra Condensed';">
    


    <!-- Start container-fluid -->
    <div class="container-fluid">



    <!-- Start container row content -->
    <div class="row content">
    


    <!-- Start container col-sm-3 sidenav -->
    <div class="col-sm-3 sidenav hidden-xs" id="blue-sidenav-hidden-xs">    


    <!-- Start logo page -->
    <h2 id="logo-blue">Inventário de Material<i class="fa fa-cubes" id="blue-icon-logo"></i></h2><br>
    <!-- End logo page -->



    <img src="../../Images/images.png" class="logo">


    
    <!-- Start menu-link page -->
    <ul class="nav nav-pills nav-stacked">


    <li id="list-blue"><a id="menu-blue" href="../ViewForms/PainelAnalista.php">Painel Administrativo<i class="fa fa-user " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioCadastroAuxiliar.php">Relatório Cadastro Auxiliar<i class="fa fa-puzzle-piece " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioProduto.php">Relatório Produto<i class="fa fa-cube " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioNotaFiscal.php">Relatório Nota Fiscal<i class="fa fa-cart-plus " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>

    
    </ul>
    


    <br>
    <!-- End menu-link page -->



    </div>
    <!-- End container col-sm-3 sidenav -->



    <!-- Start container loader page -->
    <div id="loader"></div>
    <!-- End container loader page -->



    <!-- Start container animate-bottom -->
    <div style="display:none;" id="blue-animate" class="animate-bottom">



    <!-- Start container col-sm-9 -->
    <div class="col-sm-9" id="blue-col-sm-9">
    


    <!-- Start container well -->
    <div class="well" id="well-zero"><br>


    <div class="container-fluid">
    


    <!-- Botão de sair -->
    <button id="blue-btn-sign-out" onclick="window.location.href='../../ViewLogout/LogoutSistema.php';"><i class="fa fa-sign-out"></i></button>
  

    <!-- Nome do usuário -->
    <p id="blue-text-session-user">ANALISTA -  <?php echo $_SESSION['usuarioNome'];?></p>
    

    </div>


    <br>


    <style>
    


    .alerts {
    


    padding: 5px;
    


    background-color: transparent;




    color: #f0f0f0;
    


    }

    .closebtns {
    


    margin-left: 15px;
    


    color: #ff6600;    
    


    font-weight: bold;
    


    float: right;
    


    font-size: 22px;
    


    line-height: 20px;
    


    cursor: pointer;
    


    transition: 0.3s;
    


    }

    .closebtns:hover {
     


    color: black;
    


    }
    


    </style>
    

    <div class="alerts" style="display: none;" id="transferAlert">

<?php
// Conexão ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

$sql = "SELECT 
            T.*, 
            DC_DESTINO.NOME AS NOME_DATACENTER_DESTINO,
            DC_ORIGEM.NOME AS NOME_DATACENTER_ORIGEM,
            MAT_ORIGEM.MATERIAL AS NOME_MATERIAL_ORIGEM,
            MET_ORIGEM.METRAGEM AS METRAGEM_PRODUTO_ORIGEM,
            MAT_DESTINO.MATERIAL AS NOME_MATERIAL_DESTINO,
            MET_DESTINO.METRAGEM AS METRAGEM_PRODUTO_DESTINO,
            MO_ORIGEM.MODELO AS NOME_MODELO_ORIGEM,
            MO_DESTINO.MODELO AS NOME_MODELO_DESTINO,
            U.NOME AS NOME_USUARIO,
            DATE_FORMAT(T.DATA_TRANSFERENCIA, '%d/%m/%Y') AS DATA_FORMATADA
        FROM 
            TRANSFERENCIA T
        JOIN 
            PRODUTO P_ORIGEM ON T.IDPRODUTO_ORIGEM = P_ORIGEM.IDPRODUTO
        JOIN 
            PRODUTO P_DESTINO ON T.IDPRODUTO_DESTINO = P_DESTINO.IDPRODUTO
        JOIN 
            MATERIAL MAT_ORIGEM ON P_ORIGEM.IDMATERIAL = MAT_ORIGEM.IDMATERIAL
        JOIN 
            METRAGEM MET_ORIGEM ON P_ORIGEM.IDMETRAGEM = MET_ORIGEM.IDMETRAGEM
        JOIN 
            MODELO MO_ORIGEM ON P_ORIGEM.IDMODELO = MO_ORIGEM.IDMODELO
        JOIN 
            MATERIAL MAT_DESTINO ON P_DESTINO.IDMATERIAL = MAT_DESTINO.IDMATERIAL
        JOIN 
            METRAGEM MET_DESTINO ON P_DESTINO.IDMETRAGEM = MET_DESTINO.IDMETRAGEM
        JOIN 
            MODELO MO_DESTINO ON P_DESTINO.IDMODELO = MO_DESTINO.IDMODELO
        JOIN 
            DATACENTER DC_DESTINO ON P_DESTINO.IDDATACENTER = DC_DESTINO.IDDATACENTER
        JOIN 
            DATACENTER DC_ORIGEM ON P_ORIGEM.IDDATACENTER = DC_ORIGEM.IDDATACENTER
        JOIN 
            USUARIO U ON T.IDUSUARIO = U.IDUSUARIO
        WHERE 
            T.SITUACAO = 'Pendente'";

$result = $conn->query($sql);

if ($result === false) {
    echo "Erro na consulta: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        echo "<script>document.getElementById('transferAlert').style.display = 'block';</script>";

        while ($row = $result->fetch_assoc()) {
            // Conversão da data para formato brasileiro
            $dateformated = $row['DATA_FORMATADA'];
?>
            <span class="closebtns" onclick="this.parentElement.style.display='none';">&times;</span>

            <div id="blue-line-title-btn-painel-alert">
                <p id="blue-title-btn-painel-alert">Transferência Pendente <i class="fa fa-warning" id="blue-icon-btn-painel"></i></p>
            </div>

            <table class="table table-bordered" id="blue-table-cadastro-auxiliar" style="margin-top:1%;">
                <tr id="line-blue-table-alert">
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Código Saída</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['ID']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Produto Origem</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_MATERIAL_ORIGEM']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Produto Destino</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_MATERIAL_DESTINO']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Metragem</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['METRAGEM_PRODUTO_DESTINO']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Modelo</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_MODELO_ORIGEM']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Quantidade Transferida</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['QUANTIDADE']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Datacenter Origem</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_DATACENTER_ORIGEM']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Datacenter Destino</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_DATACENTER_DESTINO']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Data Transferência</div>
                        <div id="blue-input-cdst-alert"><?php echo $dateformated; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Observação</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['OBSERVACAO']; ?></div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Analista</div>
                        <div id="blue-input-cdst-alert"><?php echo $row['NOME_USUARIO']; ?></div>
                    </td>
                </tr>
            </table>

<?php
        }
    } else {
        echo "Nenhuma transferência pendente encontrada.";
    }
}


?>

</div>


<div class="alerts" style="display: none;" id="transferAlerts">
<?php
// Conexão ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

// Nome do usuário da sessão atual
$nomeUsuarioSessao = $_SESSION['usuarioNome'];

$sql = "SELECT 
            R.*, 
            DC.NOME AS NOME_DATACENTER,
            MAT.MATERIAL AS NOME_MATERIAL,
            MET.METRAGEM AS METRAGEM_PRODUTO,
            U.NOME AS NOME_USUARIO,
            E.QUANTIDADE AS QUANTIDADE_TOTAL,
            E.RESERVADO_RESERVA AS QUANTIDADE_RESERVADA,
            R.OBSERVACAO,
            DATE_FORMAT(R.DATARESERVA, '%d/%m/%Y') AS DATA_FORMATADA,
            MO.MODELO AS NOME_MODELO
        FROM 
            RESERVA R
        JOIN 
            PRODUTO P ON R.IDPRODUTO = P.IDPRODUTO
        JOIN 
            MATERIAL MAT ON P.IDMATERIAL = MAT.IDMATERIAL
        JOIN 
            METRAGEM MET ON P.IDMETRAGEM = MET.IDMETRAGEM
        JOIN 
            DATACENTER DC ON P.IDDATACENTER = DC.IDDATACENTER
        JOIN 
            USUARIO U ON R.IDUSUARIO = U.IDUSUARIO
        JOIN 
            ESTOQUE E ON P.IDPRODUTO = E.IDPRODUTO
        JOIN 
            MODELO MO ON P.IDMODELO = MO.IDMODELO
        WHERE 
            R.SITUACAO = 'Pendente'
            AND U.NOME = '" . $conn->real_escape_string($nomeUsuarioSessao) . "'";

// Executar consulta
$result = $conn->query($sql);

if ($result === false) {
    echo "Erro na consulta: " . $conn->error;
} else {
    // Verificar se há resultados
    if ($result->num_rows > 0) {
        echo "<script>document.getElementById('transferAlerts').style.display = 'block';</script>";

        // Exibir os resultados
        while ($row = $result->fetch_assoc()) {
            echo <<<HTML
            <span class="closebtns" onclick="this.parentElement.style.display='none';">&times;</span>

            <div id="blue-line-title-btn-painel-alert">
                <p id="blue-title-btn-painel-alert">Reserva Pendente <i class="fa fa-star" id="blue-icon-btn-painel"></i></p>
            </div>

            <table class="table table-bordered" id="blue-table-cadastro-auxiliar" style="margin-top:1%;">
                <tr id="line-blue-table-alert">
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Código Reserva</div>
                        <div id="blue-input-cdst-alert">{$row['ID']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Nº WO</div>
                        <div id="blue-input-cdst-alert">{$row['NUMWO']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Produto</div>
                        <div id="blue-input-cdst-alert">{$row['NOME_MATERIAL']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Metragem</div>
                        <div id="blue-input-cdst-alert">{$row['METRAGEM_PRODUTO']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Modelo</div>
                        <div id="blue-input-cdst-alert">{$row['NOME_MODELO']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Quantidade Reservada</div>
                        <div id="blue-input-cdst-alert">{$row['QUANTIDADE_RESERVADA']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Quantidade Total</div>
                        <div id="blue-input-cdst-alert">{$row['QUANTIDADE_TOTAL']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">DataCenter</div>
                        <div id="blue-input-cdst-alert">{$row['NOME_DATACENTER']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Data Reserva</div>
                        <div id="blue-input-cdst-alert">{$row['DATA_FORMATADA']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Observação</div>
                        <div id="blue-input-cdst-alert">{$row['OBSERVACAO']}</div>
                    </td>
                    <td id="colun-blue-table-alert">
                        <div id="blue-title-listar-alert">Analista</div>
                        <div id="blue-input-cdst-alert">{$row['NOME_USUARIO']}</div>
                    </td>
                </tr>
            </table>
HTML;
        }
    } else {
        echo "Nenhuma reserva pendente encontrada para este usuário.";
    }
}


?>

</div>



    <!-- Start container search material -->
    <div class="container" id="blue-search">
    


    <!-- Start form search material -->
    <form class="example"  method="POST" action="../ViewRelatorio/SearchNotaFiscal.php">
    


    <input type="text" id="input-blue-search" name="search" autocomplete="off">



    <button type="submit" id="icon-search-blue"><i class="fa fa-search"></i></button>



    </form>
    <!-- End form search material -->



    </div>
    <!-- End container search material -->



    <br>



    <div id="blue-line-title-btn-painel">



    <p id="blue-title-btn-painel">Resultado Relatório Nota Fiscal <i class="fa fa-cart-plus" id="blue-icon-btn-painel"></i></p>


    </div>


    
    <div class="container" style="width:100%;height:85%;overflow-y:auto;">
    


    <br>



    <!-- Start código PHP search material -->
    <?php

    // Conexão e consulta ao banco de dados
    require_once('../../ViewConnection/ConnectionInventario.php');

    $search = $_POST['search'];

    // Protege contra SQL Injection escapando os caracteres especiais no input do usuário
    $search = mysqli_real_escape_string($conn, $search);

    // Ajusta a consulta SQL para pesquisar em múltiplos campos
    $result_search = "
    SELECT * 
    FROM NOTAFISCAL 
    WHERE ID LIKE '%$search%' 
    OR NUMNOTAFISCAL LIKE '%$search%' 
    OR VALORNOTAFISCAL LIKE '%$search%' 
    OR MATERIAL LIKE '%$search%' 
    OR CONECTOR LIKE '%$search%' 
    OR METRAGEM LIKE '%$search%' 
    OR QUANTIDADE LIKE '%$search%' 
    OR MODELO LIKE '%$search%' 
    OR FORNECEDOR LIKE '%$search%' 
    OR DATACENTER LIKE '%$search%' 
    LIMIT 20 
    ";

    $resultado_search = mysqli_query($conn, $result_search);

    ?>



    <!--  Start If de verificação do serach encaminahdo -->
    <?php if($result_search){ ?>



    <!--  Start repetição  do serach encaminahdo -->   
    <?php  while($rows_search = mysqli_fetch_array($resultado_search)){ ?>



   <!-- Start código PHP para conversão da data, para modelo brasileiro -->
   <?php 
   


   $date = strtotime($rows_search['DATARECEBIMENTO']);
   // $data agora é uma inteiro timestamp



   $dateformated = date("d/m/Y", $date);
   // date() formatou o $date para d/m/Y
   

   $datetwo = strtotime($rows_search['DATACADASTRO']);
   // $data agora é uma inteiro timestamp
   


   $dateformatetwo = date("d/m/Y", $datetwo);
   // date() formatou o $date para d/m/Y



   ?>
   <!-- End código PHP para conversão da data, para modelo brasileiro -->
        
    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">



    <tr id="line-blue-table-hover">



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Código</p> 



    <p id="blue-text-table-exibicao"><?php echo $rows_search['ID']; ?></p>   



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Nº Nota Fiscal</p>



    <p id="blue-text-table-exibicao"><?php echo $rows_search['NUMNOTAFISCAL']; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Valor Nota Fiscal</p>



    <p id="blue-text-table-exibicao">R$<?php echo $rows_search['VALORNOTAFISCAL']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Material</p>



    <p id="blue-text-table-exibicao"><?php echo $rows_search['MATERIAL']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Metragem</p>



    <p id="blue-text-table-exibicao"><?php echo $rows_search['METRAGEM']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Modelo</p>



    <p id="blue-text-table-exibicao"><?php echo $rows_search['MODELO']; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Quantidade</p> 



    <p id="blue-text-table-exibicao"><?php echo $rows_search['QUANTIDADE']; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Fornecedor</p> 



    <p id="blue-text-table-exibicao"><?php echo $rows_search['FORNECEDOR']; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Data Recebimento</p> 



    <p id="blue-text-table-exibicao"><?php echo $dateformated; ?></p>



    </td>
    


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Data Cadastro</p> 



    <p id="blue-text-table-exibicao"><?php echo $dateformatetwo; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Data Center</p> 



    <p id="blue-text-table-exibicao"><?php echo $rows_search['DATACENTER']; ?></p>



    </td>


    
    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Modificar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/ModificarNotaFiscal.php?id=<?php echo $rows_search['ID'];?>';"><i class="fa fa-pencil" id="blue-icon-relatorio-produto"></i></div> 



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Remover</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/DeleteNotaFiscal.php?id=<?php echo $rows_search['ID'];?>';"><i class="fa fa-trash" id="blue-icon-relatorio-produto"></i></div> 



    </td>


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Download</div>
    


    <a id="blue-optios-config-dados" href='../ViewFunctions/CreateDownloadNotaFiscal.php?file=<?php echo urlencode($rows_search['FILEPATH']); ?>'><i class="fa fa-cloud-download" style="margin-left:40%;margin-top:25%;font-size:9px;" ></i></a>

    

    </td>



    </tr>



    </table>



    <?php } ?>


    
    </div> 



    <?php } ?>



    </div>



    <!-- Start container footer-page -->
    <div class="container" id="footer-page">



    <!-- Start container text footer-page -->
    <div id="group-text-footer">



    <p>Caixa Econômica Federal - Centralizadora de Suporte de Tecnologia da Informação CESTI <i class="	fa fa-gears" id="group-icon-footer"></i></p>
       
    

    </div>
    <!-- End container text footer-page -->



    </div>
    <!-- End container footer-page -->  



    </div>
    <!-- End container well -->
	
    

    </div>
    <!-- End container col-sm-9 -->



    </div>
    <!-- End container animate-bottom -->



    </div>
    <!-- End container row content -->



    </div>
    <!-- End container-fluid -->  
    


    </body>
    <!-- End body page -->
        

    
    </html>
    <!-- End html page -->