    <!-- Início da sessão PHP -->
    <?php session_start(); ?>



    <!-- Início do carregador JavaScript -->
    <script>



    var myVar;



    function myFunction() {
    
    
    myVar = setTimeout(showPage, 500);
    


    }



    function showPage() {
    


    // Esconde o loader e mostra o conteúdo principal após um intervalo
        


    document.getElementById("loader").style.display = "none";
        


    document.getElementById("blue-animate").style.display = "block";
    


    }   



    </script>
    <!-- Fim do carregador JavaScript -->



    <!-- Início do script de controle de clique direito -->
    <script>




    // Bloqueia o menu de contexto para evitar cópias não autorizadas
    document.oncontextmenu = function() {



    return false;
    


    }



    </script>
    <!-- Fim do script de controle de clique direito -->



    <!DOCTYPE html>
    


    <html lang="pt-br">



    <!-- Início do cabeçalho da página -->
    <head>
    


    <!-- Charset da página -->
    <meta charset="UTF-8">



    <!-- Garante compatibilidade com o IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">



    <!-- Responsividade para dispositivos móveis -->
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- Biblioteca jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



    <!-- Biblioteca Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>



    <!-- Biblioteca Font Awesome -->
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Biblioteca Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



    <!-- Estilo CSS personalizado -->
    <link rel="stylesheet" href="../CSS/BlueArt.css">



    <!-- Biblioteca Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@0,1..1000;1,1..1000&display=swap">



    <!-- Título da página -->
    <title>Sistema de Inventário - Painel Administrativo</title>



    <!-- Estilo CSS para a barra lateral -->
    <style>



    /* Define a altura do conteúdo para que .sidenav possa ser 100% */
    .row.content { height: 550px }



    /* Define a cor de fundo cinza e a altura de 100% */
    .sidenav {
    


    background-color: #ffffff;
    


    height: auto;

    

    }



    /* Em telas pequenas, ajusta a altura para 'auto' */
    @media screen and (max-width: 767px) {
    


    .row.content { height: auto; }

    

    }
    


    </style>
    


    </head>
    <!-- Fim do cabeçalho da página -->



    <!-- Início da página -->
    <body oncontextmenu="return false" onload="myFunction()" style="margin: 0; font-family: 'Sofia Sans Extra Condensed';">



    <!-- Início do container fluid -->
    <div class="container-fluid">



    <!-- Início da linha de conteúdo -->
    <div class="row content">



    <!-- Coluna lateral esquerda (sidenav) -->
    <div class="col-sm-3 sidenav hidden-xs" style="background-color: #f0f0f0;">



    <!-- Logo -->
    <h2 id="logo-blue">Inventário de Material<i class="fa fa-cubes" id="blue-icon-logo"></i></h2><br>



    <!-- Start menu-link page -->
    <ul class="nav nav-pills nav-stacked">


    <li id="list-blue"><a id="menu-blue" href="../ViewForms/PainelAdministrativo.php">Painel Administrativo<i class="fa fa-user " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioCadastroAuxiliar.php">Relatório Cadastro Auxiliar<i class="fa fa-puzzle-piece " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioProduto.php">Relatório Produto<i class="fa fa-cube " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    <li id="list-blue"><a id="menu-blue" href="../ViewRelatorio/RelatorioNotaFiscal.php">Relatório Nota Fiscal<i class="fa fa-cart-plus " id="blue-icon-btn-painel" style="margin-left:1%;"></i></a></li><br>



    </ul>
    <!-- End menu-link page -->



    </div>
    <!-- Fim da coluna lateral esquerda -->



    <!-- Container para o loader -->
    <div id="loader"></div>



    <!-- Container principal que será animado -->
    <div style="display:none;" id="blue-animate" class="animate-bottom">



    <!-- Coluna principal -->
    <div class="col-sm-9" style="margin-top: -20px; background-color: #f0f0f0; border-radius: 0px;">



    <!-- Container well -->
    <div class="well" id="well-zero"><br>
    <!-- Conteúdo principal da página -->


     <!-- Título da seção de cadastros auxiliares -->
    <div id="blue-line-title-btn-painel">
    


    <p id="blue-title-btn-painel">Cadastrar Nova Senha  <i class="fa fa-key" id="blue-icon-btn-painel"></i></p>
    


    </div>


    <br>

    <div class="container-fluid" style="margin-left:25%;">


    <!-- Formulário para cadastrar  -->
    <form method="POST" action="../ViewTrocarSenha/CreateNovaSenhaUsuario.php">



    <!-- Tabela de cadastro auxiliar -->
    <table class="table table-bordered" style="width:60%;height:auto;">
    


    <tr id="line-blue-table">



    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Nova Senha

    

    </div>
    


    <input type="password" id="blue-input-cdst" name="NovaSenha" value="" autocomplete="off" required /><br>
    


    </td>


    <td id="colun-blue-table">
    


    <div id="blue-title-listar">
    


    Confirmar Senha

    

    </div>
    


    <input type="password" id="blue-input-cdst" name="ConfirmarSenha" value="" autocomplete="off" required /><br>
    


    </td>



    </table>



    <button type="submit" id="blue-button-nova-senha">Cadastrar Nova Senha <i class="fa fa-key" id="blue-icon-btn-painel"></i></button>



    </form>


    </div>



    <br>



    <br>



    <br>



    <br>
    


    <br>



    <br>



    <br>



    <br>
    


    <br>



    <br>



    <br>



    <br>
    


    <br>



    <br>



    <br>



    <br>



    <br>



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