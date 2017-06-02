<?php
require_once '../Validation/GerarData.php';


class GeraLog 
{
    public static function getData(){
        $getData = new GerarData();
       return $getData ->gerarDataHora();
    }

    function grava_log_erros_banco($arquivo,$argumentos,$erro, $usuario){
        /**
         * @desc Grava log de erros ocorridos em consultas SQL
         * @return Void
         * @param String $arquivo, $linha, $argumentos, $erro
         */

        // abrindo o arquivo para gravar colocando o ponteiro de escrita na ultima linha
        $abre_log = fopen('../log/log.txt', 'a');
        // setando a data atual
        $data = self::getData();
        // definindo a mensagem a ser gravada
        $mensagem = htmlspecialchars("Usuario: $usuario - $data - $arquivo  - Argumentos: $argumentos - ErroMysql: $erro \n");

        // escrevendo no arquivo de log
        @fwrite($abre_log,$mensagem);

        // fechando o arquivo de log
        @fclose($abre_log);

    }
}


