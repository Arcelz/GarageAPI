<?php

/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 31/07/2017
 * Time: 22:35
 */
class UPermissao
{
    final public static function modulo($modulo)
    {
        $data = "";
        switch ($modulo) {
            case 1:
                $data = [
                    "usuarioCriar" => true,
                    "usuarioDeletar" => true,
                    "usuarioVisualizar" => true,
                    "admin" => true,
                    "permissaoVisualizar" => true,
                    "permissaoCriar" => true,
                    "funcionarioCriar" => true,
                    "funcionarioVisualizar" => true,
                    "funcionarioDeletar" => true,
                    "grupoVisualizar" => true,
                    "grupoCriar" => true,
                    "grupoDeletar" => true,
                ];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
    }
}

