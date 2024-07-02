    <!-- Start PHP Session -->
    <?php session_start();?>
    


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
    <title>Sistema de Inventário - Painel Administrativo</title>	
    


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
    <div class="col-sm-3 sidenav hidden-xs" style="background-color:#f0f0f0;">    


    <!-- Start logo page -->
    <h2 id="logo-blue">Inventário de Material<i class="fa fa-cubes" id="blue-icon-logo"></i></h2><br>
    <!-- End logo page -->



    <!-- Start menu-link page -->
    <ul class="nav nav-pills nav-stacked">


    <li id="list-blue"><a id="menu-blue" href="../ViewForms/PainelAdministrativo.php">Painel Administrativo<i class="fa fa-user " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



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
    <div class="col-sm-9" style="margin-top:-20px;background-color:#f0f0f0;border-radius:0px;">



    <!-- Start container well -->
    <div class="well" id="well-zero"><br>



    <!-- Botão de sair -->
    <button id="blue-btn-sign-out" onclick="window.location.href='../../ViewLogout/LogoutSistema.php';"><i class="fa fa-sign-out"></i></button>


    <br>



    <style>
    


    .alert {
    


    padding: 5px;
    


    background-color: #ff0000;
    


    color: #f0f0f0;
    

    
    font-size: 14px;



    }

    .closebtn {
    


    margin-left: 15px;
    


    color: #f0f0f0;    
    


    font-weight: bold;
    


    float: right;
    


    font-size: 22px;
    


    line-height: 20px;
    


    cursor: pointer;
    


    transition: 0.3s;
    


    }

    .closebtn:hover {
     


    color: black;
    


    }
    


    </style>
    

    
    <div class="alert">



    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
    


    A transferência não pode ser realizada. O estoque do produto de origem é insuficiente <i class='fa fa-times-circle'></i>
    

    
    </div>


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



// Conexão e consulta ao banco de dados
require_once('../../ViewConnection/ConnectionInventario.php');

$sql = "SELECT 
            T.*, 
            DC_DESTINO.NOME AS NOME_DATACENTER_DESTINO,
            DC_ORIGEM.NOME AS NOME_DATACENTER_ORIGEM,
            MAT_ORIGEM.MATERIAL AS NOME_MATERIAL_ORIGEM,
            MET_ORIGEM.METRAGEM AS METRAGEM_PRODUTO_ORIGEM,
            MAT_DESTINO.MATERIAL AS NOME_MATERIAL_DESTINO,
            MET_DESTINO.METRAGEM AS METRAGEM_PRODUTO_DESTINO,
            U.NOME AS NOME_USUARIO
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
            MATERIAL MAT_DESTINO ON P_DESTINO.IDMATERIAL = MAT_DESTINO.IDMATERIAL
        JOIN 
            METRAGEM MET_DESTINO ON P_DESTINO.IDMETRAGEM = MET_DESTINO.IDMETRAGEM
        JOIN 
            DATACENTER DC_DESTINO ON P_DESTINO.IDDATACENTER = DC_DESTINO.IDDATACENTER
        JOIN 
            DATACENTER DC_ORIGEM ON P_ORIGEM.IDDATACENTER = DC_ORIGEM.IDDATACENTER
        JOIN 
            USUARIO U ON T.IDUSUARIO = U.IDUSUARIO
        WHERE 
            T.SITUACAO = 'Pendente'";



$result = $conn->query($sql);



if ($result->num_rows > 0) { 



echo "<script>document.getElementById('transferAlert').style.display = 'block';</script>";



while ($row = $result->fetch_assoc()) { ?>
    

<!-- Start código PHP para conversão da data, para modelo brasileiro -->
<?php 



$date = strtotime($row['DATA_TRANSFERENCIA']);
// $data agora é uma inteiro timestamp



$dateformated = date("d/m/Y", $date);
// date() formatou o $date para d/m/Y



?>
<!-- End código PHP para conversão da data, para modelo brasileiro -->
<span class="closebtns" onclick="this.parentElement.style.display='none';">&times;</span> 



<!-- Título da seção de cadastros auxiliares -->
<div id="blue-line-title-btn-painel-alert">



<p id="blue-title-btn-painel-alert">Transferência Pendente  <i class="fa fa-warning" id="blue-icon-btn-painel"></i></p>



</div>


<?php echo "<table class='table table-bordered' id='blue-table-cadastro-auxiliar' style='margin-top:1%;'>";?>
<?php echo "<tr id='line-blue-table-alert'>";?>       
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Código Saída</div>  <div id='blue-input-cdst-alert'>"   . $row['ID'] . "</div></td>" ?>
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Produto Origem</div> <div id='blue-input-cdst-alert'>" . $row['NOME_MATERIAL_ORIGEM'] . "</div></td>" ?> 
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Produto Destino</div> <div id='blue-input-cdst-alert'>" . $row['NOME_MATERIAL_DESTINO'] . "</div></td>" ?> 
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Metragem</div> <div id='blue-input-cdst-alert'>" . $row['METRAGEM_PRODUTO_DESTINO'] . "</div></td>" ?>
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Quantidade Transferida</div> <div id='blue-input-cdst-alert'>" . $row['QUANTIDADE'] . "</div></td>" ?>
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Site Origem</div> <div id='blue-input-cdst-alert'>" . $row['NOME_DATACENTER_ORIGEM'] . "</div></td>" ?>
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Site Destino</div> <div id='blue-input-cdst-alert'>" . $row['NOME_DATACENTER_DESTINO'] . "</div></td>" ?>  
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Data Transferência</div> <div id='blue-input-cdst-alert'>"  . $dateformated . "</div></td>"?> 
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Observação</div> <div id='blue-input-cdst-alert'>" . $row['OBSERVACAO'] . "</div></td>" ?> 
<?php echo "<td id='colun-blue-table-alert'><div id='blue-title-listar-alert'>Analista</div> <div id='blue-input-cdst-alert'>" . $row['NOME_USUARIO'] . "</div></td>" ?> 
<?php echo "</tr>";?> 
<?php echo "</table>";?>        



<?php } ?>



<?php } ?>



</div>

    
    <!-- Título da seção de cadastros auxiliares -->
    <div id="blue-line-title-btn-painel">
    


    <p id="blue-title-btn-painel">Cadastros Auxiliares <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></p>
    


    </div>


    <br>


    <!-- Tabela de cadastro auxiliar -->
    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">
    


    <tr id="line-blue-table">



    <!-- Formulário para cadastrar Material -->
    <form method="POST" action="../ViewFunctions/CreateMaterial.php">
    


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Material

    

    </div>
    


    <input type="text" id="blue-input-cdst" name="Material" value="" autocomplete="off" required /><br>
    


    <button type="submit" id="blue-btn-cadastro-auxiliar">Cadastrar <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></button>
    


    </td>
    


    </form>



    <!-- Formulário para cadastrar Conector -->
    <form method="POST" action="../ViewFunctions/CreateConector.php">
    


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Conector
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="Conector" value="" autocomplete="off" required /><br>
    


    <button type="submit" id="blue-btn-cadastro-auxiliar">Cadastrar <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></button>
    


    </td>
    


    </form>



    <!-- Formulário para cadastrar Metragem -->
    <form method="POST" action="../ViewFunctions/CreateMetragem.php">
    


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Metragem
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="Metragem" value="" autocomplete="off" required /><br>
    


    <button type="submit" id="blue-btn-cadastro-auxiliar">Cadastrar <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></button>
    


    </td>
    


    </form>

    <!-- Formulário para cadastrar Modelo -->
    <form method="POST" action="../ViewFunctions/CreateModelo.php">
    


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Modelo
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="Modelo" value="" autocomplete="off" required /><br>
    


    <button type="submit" id="blue-btn-cadastro-auxiliar">Cadastrar <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></button>
    


    </td>
    


    </form>



    <!-- Formulário para cadastrar Fornecedor -->
    <form method="POST" action="../ViewFunctions/CreateFornecedor.php">
    
    
    
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Fornecedor
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="Fornecedor" value="" autocomplete="off" required /><br>
    


    <button type="submit" id="blue-btn-cadastro-auxiliar">Cadastrar <i class="fa fa-puzzle-piece" id="blue-icon-btn-painel"></i></button>
    


    </td>
    


    </form>



    </tr>
    


    </table>



    <!-- Título da seção de cadastro de produto -->
    <div id="blue-line-title-btn-painel">
    


    <p id="blue-title-btn-painel">Cadastro de Produto <i class="fa fa-plus-circle" id="blue-icon-btn-painel"></i></p>
    


    </div>



    <br>


    
    <!-- Tabela para formulário de cadastro -->
    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">
    


    <form method="POST" action="../ViewFunctions/CreateProduto.php" enctype="multipart/form-data">
    


    <!-- Linha da tabela -->
    <tr id="line-blue-table">



    <!-- Coluna 3: Material -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Material
    


    </div>
    


    <select id="select-form" name="Material">
    


    <?php
    // Conexão e consulta ao banco de dados
    require_once('../../ViewConnection/ConnectionInventario.php');
    


    $consulta = "SELECT * FROM MATERIAL ORDER BY IDMATERIAL";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    


    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['MATERIAL'] . '">' . $dado['MATERIAL'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    <!-- Coluna 4: Conector -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    

    
    Conector
    


    </div>
    


    <select id="select-form" name="Conector">



    <?php
    



    // Consulta ao banco de dados para listagem de conectores
    $consulta = "SELECT * FROM CONECTOR ORDER BY IDCONECTOR";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['CONECTOR'] . '">' . $dado['CONECTOR'] . '</option>';
    


    }
    ?>
    


    </select>
    


    </td>



    <!-- Coluna 5: Metragem -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Metragem
    


    </div>
    


    <select id="select-form" name="Metragem">
    


    <?php
    


    // Consulta ao banco de dados para listagem de metragens
    $consulta = "SELECT * FROM METRAGEM ORDER BY IDMETRAGEM";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['METRAGEM'] . '">' . $dado['METRAGEM'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    <!-- Coluna 6: Modelo -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">



    Modelo
    


    </div>
    


    <select id="select-form" name="Modelo">
    


    <?php
   
   

    // Consulta ao banco de dados para listagem de modelos
    $consulta = "SELECT * FROM MODELO ORDER BY IDMODELO";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['MODELO'] . '">' . $dado['MODELO'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    </tr>



    <!-- Segunda linha da tabela -->
    <tr id="line-blue-table">


    <!-- Coluna 8: Fornecedor -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Fornecedor
    


    </div>
    


    <select id="select-form" name="Fornecedor">
    


    <?php
    


    // Consulta ao banco de dados para listagem de fornecedores
    $consulta = "SELECT * FROM FORNECEDOR ORDER BY FORNECEDOR";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['FORNECEDOR'] . '">' . $dado['FORNECEDOR'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>


    <!-- Coluna 7: Quantidade -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Quantidade



    </div>
    


    <input type="number" id="blue-input-cdst" name="Quantidade" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 10: Data Cadastro -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Data Cadastro
    


    </div>
    


    <input type="date" id="blue-input-cdst" name="DataCadastro" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 11: Data Center -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Data Center
    


    </div>
    


    <select id="select-form" name="DataCenter">
    


    <option id="select-option-form" value="CTC">CTC</option>
    


    <option id="select-option-form" value="DTC">DTC</option>
    


    </select>
    


    </td>


    
    </tr>
    


    </table>



    <!-- Botão de submit -->
    <button type="submit" id="blue-btn-table-cadastro-produto">Cadastrar Produto <i class="fa fa-plus-circle" id="blue-icon-btn-painel"></i></button>
    


    </form>



    <!-- Título da seção de cadastro de produto -->
    <div id="blue-line-title-btn-painel">
    


    <p id="blue-title-btn-painel">Cadastro de Nota Fiscal  <i class="fa fa-cart-plus" id="blue-icon-btn-painel"></i></p>
    


    </div>



    <br>


    
    <!-- Tabela para formulário de cadastro -->
    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">
    


    <form method="POST" action="../ViewFunctions/CreateNotaFiscal.php" enctype="multipart/form-data">
    


    <!-- Linha da tabela -->
    <tr id="line-blue-table">
    


    <!-- Coluna 1 -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Nº Nota Fiscal
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="NumNotaFiscal" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 2 -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Valor Nota Fiscal
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="ValorNotaFiscal" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 3: Material -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Material
    


    </div>
    


    <select id="select-form" name="Material">
    


    <?php
    // Conexão e consulta ao banco de dados
    require_once('../../ViewConnection/ConnectionInventario.php');
    


    $consulta = "SELECT * FROM MATERIAL ORDER BY IDMATERIAL";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    


    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['MATERIAL'] . '">' . $dado['MATERIAL'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    <!-- Coluna 4: Conector -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    

    
    Conector
    


    </div>
    


    <select id="select-form" name="Conector">



    <?php
    



    // Consulta ao banco de dados para listagem de conectores
    $consulta = "SELECT * FROM CONECTOR ORDER BY IDCONECTOR";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['CONECTOR'] . '">' . $dado['CONECTOR'] . '</option>';
    


    }
    ?>
    


    </select>
    


    </td>



    <!-- Coluna 5: Metragem -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Metragem
    


    </div>
    


    <select id="select-form" name="Metragem">
    


    <?php
    


    // Consulta ao banco de dados para listagem de metragens
    $consulta = "SELECT * FROM METRAGEM ORDER BY IDMETRAGEM";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['METRAGEM'] . '">' . $dado['METRAGEM'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    <!-- Coluna 6: Modelo -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">



    Modelo
    


    </div>
    


    <select id="select-form" name="Modelo">
    


    <?php
   
   

    // Consulta ao banco de dados para listagem de modelos
    $consulta = "SELECT * FROM MODELO ORDER BY IDMODELO";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['MODELO'] . '">' . $dado['MODELO'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>
    


    </tr>



    <!-- Segunda linha da tabela -->
    <tr id="line-blue-table">



    <!-- Coluna 7: Quantidade -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Quantidade



    </div>
    


    <input type="number" id="blue-input-cdst" name="Quantidade" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 8: Fornecedor -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Fornecedor
    


    </div>
    


    <select id="select-form" name="Fornecedor">
    


    <?php
    


    // Consulta ao banco de dados para listagem de fornecedores
    $consulta = "SELECT * FROM FORNECEDOR ORDER BY IDFORNECEDOR";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error());



    // Loop para exibir opções
    while ($dado = $con->fetch_array()) {
    


    echo '<option id="select-option-form" value="' . $dado['FORNECEDOR'] . '">' . $dado['FORNECEDOR'] . '</option>';
    


    }
    


    ?>
    


    </select>
    


    </td>



    <!-- Coluna 9: Data Recebimento -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Data Recebimento
    


    </div>
    


    <input type="date" id="blue-input-cdst" name="DataRecebimento" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 10: Data Cadastro -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Data Cadastro
    


    </div>
    


    <input type="date" id="blue-input-cdst" name="DataCadastro" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 11: Data Center -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Data Center
    


    </div>
    


    <select id="select-form" name="DataCenter">
    


    <option id="select-option-form" value="CTC">CTC</option>
    


    <option id="select-option-form" value="DTC">DTC</option>
    


    </select>
    


    </td>



    <!-- Coluna 12: Arquivo Nota Fiscal -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Upload
    


    </div>
    


    <div class="container-fluid" id="blue-icon-upload">
    


    <label for="blue-input-file" class="custom-file-upload">
    


    <span><i class="fa fa-cloud-upload"></i></span>
    


    <input type="file" id="blue-input-file" name="NotaFiscalFile" accept=".pdf,.jpg,.jpeg,.png" required />
    


    </label>
    


    </div>
    


    </td>
    


    </tr>
    


    </table>



    <!-- Botão de submit -->
    <button type="submit" id="blue-btn-table-cadastro-produto">Cadastrar Nota Fiscal <i class="fa fa-cart-plus" id="blue-icon-btn-painel"></i></button>
    


    </form>


    <!-- Título da seção de cadastro de produto -->
    <div id="blue-line-title-btn-painel">
    


    <p id="blue-title-btn-painel">Cadastro de Usuário  <i class="fa fa-user-plus" id="blue-icon-btn-painel"></i></p>
    


    </div>



    <br>


    
    <!-- Tabela para formulário de cadastro -->
    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">
    


    <form method="POST" action="../ViewFunctions/CreateNovoUsuario.php">
    


    <!-- Linha da tabela -->
    <tr id="line-blue-table">
    


    <!-- Coluna 1 -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Nome Usuário
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="NomeUsuario" value="" autocomplete="off" required />
    


    </td>




    <!-- Coluna 2 -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Código Usuário
    


    </div>
    


    <input type="text" id="blue-input-cdst" name="CodigoUsuario" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 3: Material -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Senha
    


    </div>
    


    <input type="password" id="blue-input-cdst" name="SenhaUsuario" value="" autocomplete="off" required />
    


    </td>



    <!-- Coluna 4: Conector -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    

    
    E-mail 
    


    </div>
    

    <input type="text" id="blue-input-cdst" name="EmailUsuario" value="" autocomplete="off" required />
    
    


    </td>


    <!-- Coluna 5: Metragem -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


   DataCenter
    


    </div>
    


    <select id="select-form" name="DataCenter">
    


    <option value="CTC">CTC</option>



    <option value="DTC">DTC</option>



    </select>
    


    </td>



    <!-- Coluna 5: Metragem -->
    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Nivel de Acesso
    


    </div>
    


    <select id="select-form" name="NiveldeAcesso">
    


    <option value="1">Gestor</option>



    <option value="2">Preposto</option>



    <option value="3">Analista</option>



    <option value="4">Técnico</option>



    </select>
    


    </td>
    


    </td>
    


    </tr>



    </table>



    <!-- Botão de submit -->
    <button type="submit" id="blue-btn-table-cadastro-produto">Cadastrar Usuário <i class="fa fa-user-plus" id="blue-icon-btn-painel"></i></button>
    


    </form>



    <!-- Início do container footer-page -->
    <div class="container-fluid" id="footer-page">
    
    

    <!-- Início do container text footer-page -->
    <div id="group-text-footer">
    


    <p>Caixa Econômica Federal - Centralizadora de Suporte de Tecnologia da Informação CESTI <i class="fa fa-gears" id="group-icon-footer"></i></p>

    

    </div>
    <!-- Fim do container text footer-page -->

    

    </div>
    <!-- Fim do container footer-page -->

    
    </div>
    <!-- Fim do container principal -->



    </div>
    <!-- Fim do container well -->

    

    </div>
    <!-- Fim do container animate-bottom -->
    


    </div>
    <!-- Fim da coluna de tamanho médio (9/12) -->



    </div>
    <!-- Fim da linha de conteúdo -->
    


    </div>
    <!-- Fim do container-fluid -->



    <!-- Script opcional ou links para scripts -->
    <!-- ... -->
   
    

    </body>
    <!-- Fim do body page -->



    </html>
    <!-- Fim do HTML page -->