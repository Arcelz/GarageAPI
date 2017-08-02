<?php

/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 31/07/2017
 * Time: 20:13
 */
class Arquivo
{
    final public static function retornaConteudo($caminho){
        $ponteiro = fopen($caminho,"r");
        $str = "";
        while (!feof($ponteiro)){
            $str .= fgets($ponteiro,filesize($caminho));
        }
        fclose($ponteiro);
//        echo $str;
        return $str;
    }
}