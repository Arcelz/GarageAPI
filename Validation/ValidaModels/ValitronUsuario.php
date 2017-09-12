<?php

/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 12/09/2017
 * Time: 12:29
 */

use Valitron\Validator;

class ValitronUsuario
{
    public function __construct()
    {
        Validator::lang('pt-br');
    }
    public function validaUsuarioPost($data)
    {

        $v = new Validator($data);
        $v->rule('required', ['login', 'email', 'senha', 'funcionario_id', 'grupo_id']);
        $v->rule('email', 'email');
        $v->rule('integer', ['funcionario_id', 'grupo_id']);
        $v->rule('lengthMin', 'login', 4);
        $v->rule('lengthMax', 'login', 30);
        $v->rule('lengthMin', 'senha', 6);
        $v->rule('lengthMax', 'senha', 30);
        if ($v->validate()) {
            return true;
        } else {
            $data="";
            foreach ($v->errors() as $key => $value){
                $data .= implode(',',$value);
            }
            return $data;
        }
    }
}