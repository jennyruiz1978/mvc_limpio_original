<?php


class Usuario{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function obtenerUsuarios(){
        $this->db->query('SELECT * FROM usuarios');

        $resultado = $this->db->registros();

        return $resultado;
    }

    public function agregarUsuario($datos){

        $this->db->query("INSERT INTO usuarios (nombre,mail,telefono,password,rol) VALUES (:nombre, :mail, :telefono,:password,:rol)");

        // vincular valores

        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':mail', $datos['mail']);
        $this->db->bind(':telefono', $datos['telefono']);
        $this->db->bind(':password', $datos['password']);
        $this->db->bind(':rol', $datos['rol']);

        //Ejecutar
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }

    }

    public function obtenerUsuarioId($id){
        $this->db->query('SELECT * FROM usuarios WHERE id_usuario = :id');
        $this->db->bind(':id', $id);

        $fila = $this->db->registro();

        return $fila;
    }

    public function actualizarUsuario($datos){
        $this->db->query('UPDATE usuarios SET nombre = :nombre, mail = :mail, telefono = :telefono WHERE id_usuario = :id');
        $this->db->bind(':id', $datos['id_usuario']);
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':mail', $datos['mail']);
        $this->db->bind(':telefono', $datos['telefono']);

        //Ejecutar
        if($this->db->execute()){
            return true;

        }else {
            return false;
        }
    }

    public function borrarUsuario($datos){
        $this->db->query('DELETE FROM usuarios WHERE id_usuario = :id');
        $this->db->bind(':id', $datos['id_usuario']);


        //Ejecutar
        if($this->db->execute()){
            return true;

        }else {
            return false;
        }
    }

}