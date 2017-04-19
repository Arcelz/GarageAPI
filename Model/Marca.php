<?php

require 'Database.php';

Class Marca{



    function get_Marca($marca_id=0)
    {
        $db = Database::getInstance();


        $query=$db->query("SELECT * FROM marcas WHERE status ='ATIVO'");
        // mysqli_close($db);

        if($marca_id != 0)
        {

            $query=$db->query("SELECT * FROM marcas WHERE status ='ATIVO' AND pk_marca=".$marca_id);

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
        $fkModelo = 2;
        $status = "ATIVO";
        $nome = mysqli_real_escape_string($db,$nome);

        $result = $db->query("INSERT INTO marcas(nome,fk_modelo,status) values ('{$nome}','{$fkModelo}','{$status}')");

        if($result){

            $response = array(
                'status'=>1,
                'status_message'=>'Marca inserido com sucesso.'
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

    function update_Marca($marca_id)
    {

        $db = Database::getInstance();
        parse_str(file_get_contents('php://input'), $post_vars);
        $nome = $post_vars["nome"];

        $nome = mysqli_real_escape_string($db,$nome);


        $query=$db->query("UPDATE marcas  SET nome='{$nome}'  WHERE pk_marca=".$marca_id);

        if($query)
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Marca Atualizado com sucesso'

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

    function delete_Marca($marca_id)
    {
        $db = Database::getInstance();
        $status = 'DESATIVADO';


        $query=$db->query("UPDATE  marcas SET status='{$status}' WHERE pk_marca=".$marca_id);


        if($query > 0)
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Marca excluido com sucesso'
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

