<?php

require_once 'BancoLogin.php';
require_once '../util/UPermissao.php';
require_once '../log/GeraLog.php';
require_once '../Validation/ValidaToken.php';

class GrupoPermissao
{

    public static function getUsuario()
    {
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static function geraLog($argumentos, $erroMysql)
    {
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog->grava_log_erros_banco($arquivo, $argumentos, $erroMysql, self::getUsuario());
    }


    function insert_grupos($banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT * FROM grupos  WHERE nomeBanco = '{$banco}'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $grupoId[$row['grupo_id']] = $row['nome']; // adiciona um array de ids que ira ter no banco do usuario que fez a requisição
            }
            if (isset($grupoId[$_POST['grupo_id']])) { // verifica se existe o id se sim deleta as permicoes
                if ($grupoId[$_POST['grupo_id']]!== "gerente") {
                    $query = "SELECT * FROM permissoes_sistema WHERE nomeBanco = '{$banco}'";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $modulo = $stmt->fetch(PDO::FETCH_ASSOC);
                    $modulo = $modulo['modulo'];
                    $result = new UPermissao();
                    $result = $result->modulo($modulo);
                    $array = (Array)$_POST['permissao_id'];
                    //$array = array_unique($array, SORT_STRING);
                    $boleano = true;
                    for ($i = 0; $i < count($array); $i++) {
                        if (!isset($result[$array[$i]["id"]])) {
                            $boleano = false;
                        }
                    }
                    if ($boleano) {
                        $query = "DELETE p FROM permissoes as p WHERE grupo_id = {$_POST['grupo_id']}";
                        $stmt = $db->prepare($query);
                        $stmt->execute();

                        for ($i = 0; $i < count($array); $i++) {
                            $query = "INSERT INTO permissoes(nome,nomeBanco,grupo_id) VALUES(:nome,'$banco',:grupo_id) ";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':nome', $array[$i]["id"], PDO::PARAM_STR);
                            $stmt->bindParam(':grupo_id', $_POST['grupo_id'], PDO::PARAM_INT);
                            $stmt->execute();
                        }
                        $status = 200;
                        $status_message = 'Grupo de permissão adicionado com sucesso';

                    } else {
                        $status = 400;
                        $status_message = 'Permissão não encontrada';
                    }
                }
                else{
                    $status = 400;
                    $status_message = 'Grupo gerente não pode ser editado';
                }
            } else {
                $status = 400;
                $status_message = 'Grupo não encontrado';
            }
        } catch (PDOException $e) {
            $status = 400;
            $status_message = $e->getMessage();
        }

        $response = array(
            'status' => $status,
            'status_message' => $status_message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_grupos($grupo_id = 0, $banco)
    {
        try {
            $db = BancoLogin::conexao();
            $query = "SELECT *  FROM permissoes WHERE nomeBanco = '{$banco}'";
            if ($grupo_id != 0) {
                $query .= " AND grupo_id = :grupo_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_STR);
            } else {
                $stmt = $db->prepare($query);
            }
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response[] = $row;
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog($argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
