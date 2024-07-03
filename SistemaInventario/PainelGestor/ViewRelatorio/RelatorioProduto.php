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
    <title>Sistema de Inventário - Relatório Produto</title>	
    


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



    <button id="blue-btn-sign-out" onclick="window.location.href='../../ViewLogout/LogoutSistema.php';"><i class="fa fa-sign-out"></i></button>



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



    <!-- Start container search material -->
    <div class="container" id="blue-search">
    


    <!-- Start form search material -->
    <form class="example"  method="POST" action="SearchProduto.php">
    


    <input type="text" id="input-blue-search" name="search" autocomplete="off" placeholder="Pesquisar">



    <button type="submit" id="icon-search-blue"><i class="fa fa-search"></i></button>



    </form>
    <!-- End form search material -->



    </div>
    <!-- End container search material -->


    <br>


    <div id="blue-line-title-btn-painel">



    <p id="blue-title-btn-painel">Relatório Produto <i class="fa fa-cube" id="blue-icon-btn-painel"></i></p>



    </div>

    
    <div class="container-fluid" style="width:100%;height:87%;overflow-y:auto">
   

    
   <br>



   <?php

// Obter o ID do usuário a partir da sessão
$idUsuario = $_SESSION['usuarioId'] ?? '';

// Sanitizar o ID do usuário para evitar injeção de SQL
$idUsuario = $conn->real_escape_string($idUsuario);

// Consulta para obter o datacenter e nível de acesso do usuário
$consultaUsuario = "SELECT DATACENTER, NIVEL_ACESSO FROM USUARIO WHERE IDUSUARIO = ?";
if ($stmt = $conn->prepare($consultaUsuario)) {
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $stmt->bind_result($datacenterUsuario, $nivelAcesso);
    $stmt->fetch();
    $stmt->close();
}

// Consulta para obter os produtos
$consulta = "
    SELECT 
        p.IDPRODUTO, 
        m.MATERIAL, 
        c.CONECTOR, 
        met.METRAGEM, 
        mdo.MODELO, 
        f.FORNECEDOR, 
        p.DATACADASTRO, 
        d.NOME AS NOME_DATACENTER, 
        e.QUANTIDADE
    FROM 
        PRODUTO p
    INNER JOIN 
        MATERIAL m ON p.IDMATERIAL = m.IDMATERIAL
    INNER JOIN 
        CONECTOR c ON p.IDCONECTOR = c.IDCONECTOR
    INNER JOIN 
        METRAGEM met ON p.IDMETRAGEM = met.IDMETRAGEM
    INNER JOIN 
        MODELO mdo ON p.IDMODELO = mdo.IDMODELO
    INNER JOIN 
        FORNECEDOR f ON p.IDFORNECEDOR = f.IDFORNECEDOR
    INNER JOIN 
        ESTOQUE e ON p.IDPRODUTO = e.IDPRODUTO
    INNER JOIN 
        DATACENTER d ON p.IDDATACENTER = d.IDDATACENTER";

// Adicionar condição de datacenter se o nível de acesso não for 1
if ($nivelAcesso != 1) {
    $consulta .= " WHERE d.NOME = ?";
}

$consulta .= " ORDER BY p.IDPRODUTO";

if ($stmt = $conn->prepare($consulta)) {
    if ($nivelAcesso != 1) {
        $stmt->bind_param("s", $datacenterUsuario);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) { ?>



    <table  class="table table-bordered" id="blue-table-cadastro-auxiliar">



    <tr id="line-blue-table-hover">



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Código</p> 



    <p id="blue-text-table-exibicao"><?php echo $row['IDPRODUTO']; ?></p>   



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Material</p>



    <p id="blue-text-table-exibicao"><?php echo $row['MATERIAL']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Metragem</p>



    <p id="blue-text-table-exibicao"><?php echo $row['METRAGEM']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Conector</p>



    <p id="blue-text-table-exibicao"><?php echo $row['CONECTOR']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Modelo</p>



    <p id="blue-text-table-exibicao"><?php echo $row['MODELO']; ?></p>



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Quantidade</p> 



    <p id="blue-text-table-exibicao"><?php echo $row['QUANTIDADE']; ?></p>



    </td>
    


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">DataCenter</p> 



    <p id="blue-text-table-exibicao"><?php echo $row['NOME_DATACENTER']; ?></p>



    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Detalhar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/DetalharProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-eye" id="blue-icon-relatorio-produto"></i></div> 



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Modificar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/ModificarProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-pencil" id="blue-icon-relatorio-produto" ></i></div> 



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Remover</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/DeleteProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-trash" id="blue-icon-relatorio-produto"></i></div> 



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Acrescentar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/AcrescentarProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-level-up" id="blue-icon-relatorio-produto"></i></div> 

    

    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Diminuir</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/SubtracaoProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-level-down" id="blue-icon-relatorio-produto"></i></div> 

    

    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Sobrepor</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/SobreporProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-refresh" id="blue-icon-relatorio-produto"></i></div> 

    

    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Reservar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/ReservaProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-star" id="blue-icon-relatorio-produto"></i></div> 



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Devolver</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/DevolverProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-exchange" id="blue-icon-relatorio-produto"></i></div> 



    </td>


    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Inutilizar</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/InutilizarProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-warning" id="blue-icon-relatorio-produto"></i></div> 


    </td>



    <td id="colun-blue-table">



    <p id="blue-title-listar-exibicao">Transferir</p> 



    <div id="blue-optios-config-dados" onclick="window.location.href='../ViewForms/TransferenciaProduto.php?id=<?php echo $row['IDPRODUTO'];?>';"><i class="fa fa-retweet" id="blue-icon-relatorio-produto"></i></div> 



    </td>



    </tr>



    </table>



    <?php } ?>



    <?php } ?>

    

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

    