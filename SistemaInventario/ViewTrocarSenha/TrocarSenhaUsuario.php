    <!-- Início da sessão PHP -->
    <?php session_start(); ?>


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



    </head>
    <!-- Fim do cabeçalho da página -->



    <!-- Início da página -->
    <body class="blue-body-index" style="font-family:'Sofia Sans Extra Condensed';">


    <br>


    <!-- Start logo page -->
    <h2 id="logo-blue">Inventário de Material<i class="fa fa-cubes" id="blue-icon-logo"></i></h2><br>
    <!-- End logo page -->


     <!-- Título da seção de cadastros auxiliares -->
    <div id="blue-line-title-btn-painel" style="width:41%;margin-left:31%;">
    


    <img src="../Images/images.png" class="logo" style="margin-left:0%;">



    <p id="blue-title-btn-painel">Cadastrar Nova Senha  <i class="fa fa-key" id="blue-icon-btn-painel"></i></p>
    


    </div>


    <div class="container-fluid" style="margin-left:30%;margin-top:1%;">


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


    </body>
    <!-- Fim do body page -->


    </html>
    <!-- Fim do HTML page -->