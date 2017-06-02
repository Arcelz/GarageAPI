<?php

require 'Banco.php';
require_once '../log/GeraLog.php';
require_once  '../Validation/ValidaToken.php';

Class Marca
{

      public static function getUsuario(){
        $getUsuario = new ValidaToken();//intancia a classe de validação de token onde sera feita a verificacao do token
        $permicao = $getUsuario->usuario();
        //var_dump($permicao) ;
        return $permicao;
    }

    public static  function geraLog($argumentos, $erroMysql ){
        $arquivo = __FILE__; //pega o caminho do arquvio.
        $geraLog = new GeraLog();
        $geraLog ->grava_log_erros_banco($arquivo,$argumentos, $erroMysql, self::getUsuario());
    }



    function get_Marca($id = 0)
    {
        try {
            $db = Banco::conexao();

            //Essa query busca todos os regestritos
            $query = "SELECT * FROM marcas WHERE status ='ATIVO'";

            $response = array();
            if ($id != 0) {
                //busca pelo id. Caso o id informando nao seja certo retorna 404.
                $query .= " AND pk_marca = :id LIMIT 1";

            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();
            //var_dump($row);


            if ($row == null) {
                 $response = array(
                    'status' => 400,
                    'status_message' => 'Nao foi possivel realizar a pesquisa'
                );
                header("HTTP/1.0 400 ");

            } else {
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //$response[]= $row;
                    array_push($response, $row);
                }

            }

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Pesquisando .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);


        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert()
    {

        try {
            $db = Banco::conexao();

            $status = 'ATIVO';

            $query = "INSERT INTO marcas(nome,status) values (:nome,:status)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':nome', $_POST['nome'], PDO::PARAM_STR);            
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            $stmt->execute();
            
            $pk_marca = $db->lastInsertId();  
            $nome = $_POST['nome'];

            $response = array(
                'status' => 200,
                'status_message' => 'Marca adicionado.',
                'pk_marca' =>  $pk_marca,
                'nome' => $nome
            );
            header("HTTP/1.0 200 ");

        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            header("HTTP/1.0 400 ");
            self::getUsuario();
            $argumentos = "Iserindo.....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_Marcas($id)
    {

        try {
            $db = Banco::conexao();
            parse_str(file_get_contents('php://input'), $post_vars);

            $query = "UPDATE marcas  SET nome=:nome  WHERE pk_marca=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $post_vars['nome'], PDO::PARAM_STR);

            $stmt->execute();
            $response = array(
                'status' => 200,
                'status_message' => 'Marca Atualizado com sucesso'

            );
            header("HTTP/1.0 200 ");
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            self::getUsuario();
            $argumentos = "Update .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs


        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_Marca($id)
    {
        try {
            $db = Banco::conexao();
            $status = 'DESATIVADO';

            $query = "SELECT * FROM marcas WHERE status ='ATIVO' AND pk_marca=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetchAll();


            //Essa condição é para verificar se a url existe no servidor. Porque fazemos a consulta pelos funcionarios ativos
            if ($row == null) {
                $response = array(
                    'status' => 404,
                    'status_message' => 'Recurso nao encontrado'

                );
                header("HTTP/1.0 404 ");
            } else {
                $query = "UPDATE  marcas SET status='{$status}' WHERE pk_marca= :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $response = array(
                    'status' => 200,
                    'status_message' => 'Marca Excluido com Sucesso'
                );
                header("HTTP/1.0 200 ");
            }
        } catch (PDOException $e) {
            $response = array(
                'status' => 400,
                'status_message' => $e->getMessage()
            );
            self::getUsuario();
            $argumentos = "delete .....";
            self::geraLog( $argumentos, $e->getMessage()); //chama a função para gravar os logs

        }
        unset($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

