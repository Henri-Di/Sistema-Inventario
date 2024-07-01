<?php
session_start();

// Diretório onde os arquivos estão armazenados
$uploadDir = '../../Uploads/';

// Verifica se o nome do arquivo foi fornecido via GET
if (isset($_GET['file']) && !empty($_GET['file'])) {
    // Sanitiza o nome do arquivo para evitar ataques
    $fileName = basename($_GET['file']);
    $filePath = $uploadDir . $fileName;

    // Verifica se o arquivo existe
    if (file_exists($filePath)) {
        // Definindo cabeçalhos para forçar o download do arquivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Envia o arquivo para o navegador
        readfile($filePath);
        exit;
    } else {
        // Redireciona para uma página de erro se o arquivo não existir
        header("Location: ../ViewFail/FailDownloadNotaFiscal.php?erro=Não foi possível fazer o download do arquivo");
        exit();
    }
} else {
    // Redireciona para uma página de erro se o nome do arquivo não for fornecido
    header("Location: ../ViewFail/FailDownloadNotaFiscal.php?erro=Não foi possível fazer o download do arquivo");
    exit();
}
?>
