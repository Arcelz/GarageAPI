<?php

require 'Database.php';

Class TipoReparo{



    function get_tpReparos($cargos_id=0)
    {
        $db = Database::getInstance();

        //SELECT * FRON funcionarios AS f JOIN funcionarios_enderecos AS fe ON f.pk_funcionario = fe.fk_funcionario
        $query=$db->query("SELECT * FROM tipos_reparos WHERE status ='ATIVO'");
        // mysqli_close($db);

        if($cargos_id != 0)
        {

            $query=$db->query("SELECT * FROM tipos_reparos WHERE status ='ATIVO' AND pk_tipo=".$cargos_id);

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
        $status ='ATIVO';
        $nome = mysqli_real_escape_string($db,$nome);

        $result = $db->query("INSERT INTO tipos_reparos(nome,status) values ('{$nome}','{$status}')");

        if($result){

            $response = array(
                'status'=>1,
                'status_message'=>'Tipo de Reparo inserido com sucesso.'
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

