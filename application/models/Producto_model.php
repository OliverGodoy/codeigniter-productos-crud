<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model
{
    private $table = 'productos';

    public function __construct()
    {
        parent::__construct();
    }

    public function obtener_todos()
    {
        return $this->db->order_by('nombre', 'ASC')->get($this->table)->result();
    }

    public function obtener($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function crear($datos)
    {
        return $this->db->insert($this->table, $datos);
    }

    public function actualizar($id, $datos)
    {
        return $this->db->where('id', $id)->update($this->table, $datos);
    }

    public function eliminar($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
