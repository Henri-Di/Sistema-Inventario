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
    <link rel="stylesheet" href="./CSS/BlueArt.css">



    <!-- Google fonts library -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    


    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@0,1..1000;1,1..1000&display=swap">



	<!-- Title page -->
    <title>Sistema de Inventário - Modificar Produto</title>	
    


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


    <li id="list-blue"><a id="menu-blue" href="../ViewForms/PainelPresposto.php">Painel Administrativo<i class="fa fa-user " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



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



    <div id="blue-line-title-btn-painel">



    <p id="blue-title-btn-painel"> Modificar Nota Fiscal   <i class="fa fa-pencil" id="blue-icon-btn-painel"></i></p>



    </div>



    <br>


    
    <?php

    // Conexão e consulta ao banco de dados
    require_once('../../ViewConnection/ConnectionInventario.php'); 
    


    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);



    $consulta = "SELECT * FROM NOTAFISCAL WHERE ID='$id'";
    


    $con = mysqli_query($conn, $consulta) or die(mysqli_error($conn));
    


    $dado = $con->fetch_array();



    ?>



    <form method="POST" action="CreateModificaNotaFiscal.php">



    <input type="hidden" value="<?php echo $dado['ID']; ?>" name="id"/>



    <table class="table table-bordered" id="blue-table-cadastro-auxiliar">



    <tr id="line-blue-table">



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Nº Nota Fiscal</div>
    


    <input type="text" id="blue-input-cdst" name="NumNotaFiscal" value="<?php echo $dado['NUMNOTAFISCAL'];?>" autocomplete="off"/>
    
    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Valor Nota Fiscal</div>
    


    <input type="text" id="blue-input-cdst" name="ValorNotaFiscal" value="<?php echo $dado['VALORNOTAFISCAL'];?>" autocomplete="off"/>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Material</div>
    


    <select id="select-form" name="Material">
    


    <option value="<?php echo $dado['MATERIAL']; ?>"><?php echo $dado['MATERIAL']; ?></option>
    


    <?php
    


    $consultaMaterial = "SELECT MATERIAL FROM MATERIAL";
    


    $conMaterial = mysqli_query($conn, $consultaMaterial) or die(mysqli_error($conn));
    


    while($material = $conMaterial->fetch_array()) {
    


    echo "<option value=\"{$material['MATERIAL']}\">{$material['MATERIAL']}</option>";
    


    }
    


    ?>
    


    </select>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Conector</div>
    


    <select id="select-form" name="Conector">
    


    <option value="<?php echo $dado['CONECTOR']; ?>"><?php echo $dado['CONECTOR']; ?></option>
    


    <?php
    


    $consultaConector = "SELECT CONECTOR FROM CONECTOR";
    


    $conConector = mysqli_query($conn, $consultaConector) or die(mysqli_error($conn));
    


    while($conector = $conConector->fetch_array()) {
    


    echo "<option value=\"{$conector['CONECTOR']}\">{$conector['CONECTOR']}</option>";
   


    }
    


    ?>
    


    </select>
    


    </td>



    </tr>



    <tr id="line-blue-table">



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Metragem</div>
    


    <select id="select-form" name="Metragem">
    


    <option value="<?php echo $dado['METRAGEM']; ?>"><?php echo $dado['METRAGEM']; ?></option>
    


    <?php
    


    $consultaMetragem = "SELECT METRAGEM FROM METRAGEM";
    


    $conMetragem = mysqli_query($conn, $consultaMetragem) or die(mysqli_error($conn));
    


    while($metragem = $conMetragem->fetch_array()) {
    


    echo "<option value=\"{$metragem['METRAGEM']}\">{$metragem['METRAGEM']}</option>";
    


    }
    


    ?>
    


    </select>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Modelo</div>
    


    <select id="select-form" name="Modelo">
    


    <option value="<?php echo $dado['MODELO']; ?>"><?php echo $dado['MODELO']; ?></option>
    


    <?php
    


    $consultaModelo = "SELECT MODELO FROM MODELO";
    


    $conModelo = mysqli_query($conn, $consultaModelo) or die(mysqli_error($conn));
    


    while($modelo = $conModelo->fetch_array()) {
    


    echo "<option value=\"{$modelo['MODELO']}\">{$modelo['MODELO']}</option>";
   


    }
    


    ?>
    


    </select>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Quantidade</div>
    


    <input type="number" id="blue-input-cdst" name="Quantidade" value="<?php echo $dado['QUANTIDADE'];?>" autocomplete="off"/>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Fornecedor</div>
    


    <select id="select-form" name="Fornecedor">
    


    <option value="<?php echo $dado['FORNECEDOR']; ?>"><?php echo $dado['FORNECEDOR']; ?></option>
    


    <?php
    


    $consultaFornecedor = "SELECT FORNECEDOR FROM FORNECEDOR";
    


    $conFornecedor = mysqli_query($conn, $consultaFornecedor) or die(mysqli_error($conn));
    


    while($fornecedor = $conFornecedor->fetch_array()) {
    


    echo "<option value=\"{$fornecedor['FORNECEDOR']}\">{$fornecedor['FORNECEDOR']}</option>";
    


    }
    


    ?>
    


    </select>
    


    </td>



    </tr>



    <tr id="line-blue-table">



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Data Recebimento</div>
    


    <input type="date" id="blue-input-cdst" name="DataRecebimento" value="<?php echo $dado['DATARECEBIMENTO'];?>" autocomplete="off"/>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Data Cadastro</div>
    


    <input type="date" id="blue-input-cdst" name="DataCadastro" value="<?php echo $dado['DATACADASTRO'];?>" autocomplete="off"/>
    


    </td>



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">Data Center</div>
    


    <input type="text" id="blue-input-cdst" name="DataCenter" value="<?php echo $dado['DATACENTER'];?>" autocomplete="off"/>
    


    </td>



    </tr>



    </table>



    <button type="submit" id="blue-btn-table-cadastro-produto">Modificar Nota Fiscal <i class="fa fa-pencil"></i></button>



    </form>



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


    
    <br>
    

    <br>
    

    <br>
    

    <br>
    

    <br>
    


    <!-- Start container footer-page -->
    <div class="container" id="footer-page">
    


    <!-- Start container text footer-page -->
    <div id="group-text-footer">
    


    <p>Caixa Econômica Federal - Centralizadora de Suporte de Tecnologia da Informação CESTI <i class="	fa fa-gears" id="group-icon-footer"></i></p>
    


    </div>
    <!-- End container text footer-page -->    



    </div>
    <!-- End container footer-page -->



    </body>
    <!-- End body page -->


    
    </html>
    <!-- End html page -->