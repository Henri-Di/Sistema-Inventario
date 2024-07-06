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



    <!-- Start container col-sm-3 sidenav -->
    <div class="col-sm-3 sidenav hidden-xs" id="blue-sidenav-hidden-xs">    


    <!-- Start logo page -->
    <h2 id="logo-blue">Inventário de Material<i class="fa fa-cubes" id="blue-icon-logo"></i></h2><br>
    <!-- End logo page -->



    <img src="../Images/images.png" class="logo">


    
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
    <div class="col-sm-9" id="blue-col-sm-9">



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



    <br>


    
    <!-- Início do container footer-page -->
    <div class="container-fluid" id="footer-page">
    
    

    <!-- Início do container text footer-page -->
    <div id="group-text-footer">
    


    <p>Caixa Econômica Federal - Centralizadora de Suporte de Tecnologia da Informação CESTI <i class="fa fa-gears" id="group-icon-footer"></i></p>

    

    </div>
    <!-- Fim do container text footer-page -->

    
    <div class="container-fluid" style="display: flex; justify-content: center; align-items: center; margin-top: -15px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 192.756 192.756" style="border:none;">
        <g fill-rule="evenodd" clip-rule="evenodd">
            <path fill="#fff" d="M0 0h192.756v192.756H0V0z"/>
            <path d="M64.466 90.115l-4.258 10.61h5.891L64.485 90.07l-.019.045zM41.91 114.469l17.467-35.957h13.202l7.366 35.957H68.35l-.852-4.791H56.25l-2.588 4.791H41.91zm43.522 0l5.06-35.957h11.682l-5.059 35.957H85.432zm77.691-24.399l-4.275 10.655h5.891l-1.616-10.655zm-22.574 24.399l17.467-35.957h13.201l7.365 35.957h-11.594l-.852-4.791h-11.248l-2.588 4.791h-11.751zM114.725 97.656h14.636l10.03 16.612h-14.639l-10.027-16.612z" fill="#0d6fab"/>
            <path fill="#db8135" d="M116.246 95.104h15.129l13.139-16.54h-15.129l-13.139 16.54zM101.117 114.369h15.129l13.139-16.744h-15.129l-13.139 16.744z"/>
            <path fill="#0d6fab" d="M106.572 78.362h14.121l9.674 16.742h-14.121l-9.674-16.742zM42.257 79.8l-1.124 10.575c-4.306-5.939-14.284-1.841-15.068 5.558-.996 9.392 8.608 11.583 13.835 6.052l-1.127 10.6c-1.56.768-3.085 1.361-4.59 1.764a18.783 18.783 0 0 1-4.487.648c-1.872.039-3.567-.158-5.096-.588a12.436 12.436 0 0 1-4.134-2.002c-2.36-1.725-4.066-3.918-5.118-6.588-1.05-2.678-1.397-5.699-1.038-9.076.288-2.713.973-5.188 2.062-7.432 1.08-2.244 2.576-4.281 4.481-6.129 1.801-1.763 3.762-3.092 5.885-3.996 2.113-.895 4.421-1.373 6.915-1.425a17.085 17.085 0 0 1 4.37.46c1.425.341 2.831.873 4.234 1.579z"/>
        </g>
    </svg>
    </div>


    
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