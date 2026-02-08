<?php

namespace JairoJeffersont\Helpers;

class ForceDownload {
    public static function forcarDownload(string $caminhoArquivo): void {
        // Verifica se o arquivo existe
        if (!file_exists($caminhoArquivo)) {
            http_response_code(404);
            echo "Arquivo não encontrado.";
            exit;
        }

        // Pega o nome do arquivo
        $nomeArquivo = basename($caminhoArquivo);

        // Limpa qualquer saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Cabeçalhos para forçar download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-Length: ' . filesize($caminhoArquivo));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // Envia o arquivo
        readfile($caminhoArquivo);
        exit;
    }
}
