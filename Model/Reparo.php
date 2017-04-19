<?php

require 'Database.php';

Class Reparo{



    function get_Reparos($reparo_id=0)
    {
        $db = Database::getInstance();

        //SELECT * FRON funcionarios AS f JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario
        $query=$db->query("SELECT * FROM reparos WHERE status ='ATIVO'");
        // mysqli_close($db);

        if($reparo_id != 0)
        {

            $query=$db->query("SELECT * FROM reparos WHERE status ='ATIVO' AND pk_reparo=".$reparo_id);

        }
        mysqli_close($db);
        $response=array();
        $result=$query;

        while($row=mysqli_fetch_array($result))
        {
            $response[]=$row;
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function insert()
    {

        $db = Database::getInstance();

        $nome = isset( $_POST['nome'] ) ? $_POST['nome']  : '' ;
        $descricao = isset( $_POST['descricao'] ) ? $_POST['descricao']  : '' ;
        $valor = isset( $_POST['valor'] ) ? $_POST['valor']  : '' ;
        $fkVeiculo = 1;
        $fkTipo = 2;
        $status ='ATIVO';

        $nome = mysqli_real_escape_string($db,$nome);
        $descricao = mysqli_real_escape_string($db, $descricao);
        $valor = mysqli_real_escape_string($db,$valor);

        $result = $db->query("INSERT INTO reparos(fk_veiculo,fk_tipos,descricao,valor,status) values ('{$fkVeiculo}','{$fkTipo}','{$descricao}','{$valor}','{$status}')");

        if($result){

            $response = array(
                'status'=>1,
                'status_message'=>' Reparo inserido com sucesso.'
            );
        }
        else{
            $response=array(
                'status'=>0,
                'Error Mysql: '=>mysqli_error($db)

            );
        }
        mysqli_close($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_tpReparos($cargos_id)
    {

        $db = Database::getInstance();
        parse_str(file_get_contents('php://input'), $post_vars);
        $nome = $post_vars["nome"];

        $nome = mysqli_real_escape_string($db,$nome);


        $query=$db->query("UPDATE tipos_reparos  SET nome='{$nome}'  WHERE pk_tipo=".$cargos_id);

        if($query)
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Tipo de Reparo Atualizado com sucesso'

            );
        }
        else
        {
            $response=array(
                'status' => 0,
                'error Mysql: ' =>mysqli_error($db)
            );

        }
        mysqli_close($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_tpReparos($cargos_id)
    {
        $db = Database::getInstance();
        $status = 'DESATIVADO';


        $query=$db->query("UPDATE  tipos_reparos SET status='{$status}' WHERE pk_tipo=".$cargos_id);


        if($query > 0)
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Produto excluido com sucesso'
            );
        }
        else
        {
            $response=array(
                'status' => 0,
                'error Mysql: ' =>mysqli_error($db)
            );
        }
        mysqli_close($db);
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

