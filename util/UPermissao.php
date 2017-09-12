<?php

/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 31/07/2017
 * Time: 22:35
 */
class UPermissao
{
    public function modulo($modulo)
    {
        $data = "";
        switch ($modulo) {
            case 1:
                $data = [ // permissao de admin
                    "cargoCriar" => true,
                    "cargoVisualizar" => true,
                    "cargoDeletar" => true,
                    "clienteCriar" => true,
                    "clienteVisualizar" => true,
                    "clienteDeletar" => true,
                    "compraCriar" => true,
                    "compraVisualizar" => true,
                    "compraDeletar" => true,
                    "fornecedorCriar" => true,
                    "fornecedorVisualizar" => true,
                    "fornecedorDeletar" => true,
                    "funcionarioCriar" => true,
                    "funcionarioVisualizar" => true,
                    "funcionarioDeletar" => true,
                    "grupoVisualizar" => true,
                    "grupoCriar" => true,
                    "grupoDeletar" => true,
                    "permissaoCriar" => true,
                    "permissaoVisualizar" => true,
                    "marcaCriar" => true,
                    "marcaDeletar" => true,
                    "marcaVisualizar" => true,
                    "modeloCriar" => true,
                    "modeloDeletar" => true,
                    "modeloVisualizar" => true,
                    "relatorio" => true,
                    "reparoCriar" => true,
                    "reparoDeletetar" => true,
                    "reparoVisualizar" => true,
                    "tipoReparoCriar" => true,
                    "tipoReparoDeletar" => true,
                    "tipoReparoVisualizar" => true,
                    "tipoVeiculoCriar" => true,
                    "tipoVeiculoDeletar" => true,
                    "tipoVeiculoVisualizar" => true,
                    "usuarioCriar" => true,
                    "usuarioDeletar" => true,
                    "usuarioVisualizar" => true,
                    "veiculoCriar" => true,
                    "veiculoDeletar" => true,
                    "veiculoVisualizar" => true,
                    "vendaCriar" => true,
                    "vendaDeletar" => true,
                    "vendaVisualizar" => true,
                    "entradaVisualizar" => true,
                    "saidaVisualizar" => true
                ];
                break;
            case 999:
                $data = [ // permissao completa
                    "admin" => true,
                    "cargoCriar" => true,
                    "cargoVisualizar" => true,
                    "cargoDeletar" => true,
                    "clienteCriar" => true,
                    "clienteVisualizar" => true,
                    "clienteDeletar" => true,
                    "compraCriar" => true,
                    "compraVisualizar" => true,
                    "compraDeletar" => true,
                    "fornecedorCriar" => true,
                    "fornecedorVisualizar" => true,
                    "fornecedorDeletar" => true,
                    "funcionarioCriar" => true,
                    "funcionarioVisualizar" => true,
                    "funcionarioDeletar" => true,
                    "grupoVisualizar" => true,
                    "grupoCriar" => true,
                    "grupoDeletar" => true,
                    "permissaoCriar" => true,
                    "permissaoVisualizar" => true,
                    "marcaCriar" => true,
                    "marcaDeletar" => true,
                    "marcaVisualizar" => true,
                    "modeloCriar" => true,
                    "modeloDeletar" => true,
                    "modeloVisualizar" => true,
                    "relatorio" => true,
                    "reparoCriar" => true,
                    "reparoDeletetar" => true,
                    "reparoVisualizar" => true,
                    "tipoReparoCriar" => true,
                    "tipoReparoDeletar" => true,
                    "tipoReparoVisualizar" => true,
                    "tipoVeiculoCriar" => true,
                    "tipoVeiculoDeletar" => true,
                    "tipoVeiculoVisualizar" => true,
                    "usuarioCriar" => true,
                    "usuarioDeletar" => true,
                    "usuarioVisualizar" => true,
                    "veiculoCriar" => true,
                    "veiculoDeletar" => true,
                    "veiculoVisualizar" => true,
                    "vendaCriar" => true,
                    "vendaDeletar" => true,
                    "vendaVisualizar" => true,
                    "entradaVisualizar" => true,
                    "saidaVisualizar" => true
                ];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
    }
}

