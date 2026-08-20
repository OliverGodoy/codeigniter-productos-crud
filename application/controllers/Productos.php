<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Producto_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['productos'] = $this->Producto_model->obtener_todos();
        $this->load->view('templates/header', ['titulo' => 'Productos']);
        $this->load->view('productos/index', $data);
        $this->load->view('templates/footer');
    }

    public function crear()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
            $this->form_validation->set_rules('precio', 'Precio', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('stock', 'Stock', 'required|integer|greater_than_equal_to[0]');

            if ($this->form_validation->run()) {
                $this->Producto_model->crear([
                    'nombre' => $this->input->post('nombre'),
                    'precio' => $this->input->post('precio'),
                    'stock' => $this->input->post('stock'),
                ]);

                $this->session->set_flashdata('mensaje', 'Producto creado correctamente.');
                redirect('productos');
                return;
            }
        }

        $this->load->view('templates/header', ['titulo' => 'Nuevo producto']);
        $this->load->view('productos/crear');
        $this->load->view('templates/footer');
    }

    public function editar($id)
    {
        $producto = $this->Producto_model->obtener($id);

        if (!$producto) {
            show_404();
            return;
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
            $this->form_validation->set_rules('precio', 'Precio', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('stock', 'Stock', 'required|integer|greater_than_equal_to[0]');

            if ($this->form_validation->run()) {
                $this->Producto_model->actualizar($id, [
                    'nombre' => $this->input->post('nombre'),
                    'precio' => $this->input->post('precio'),
                    'stock' => $this->input->post('stock'),
                ]);

                $this->session->set_flashdata('mensaje', 'Producto actualizado correctamente.');
                redirect('productos');
                return;
            }

            // Muestra en el formulario lo que el usuario intentó guardar, no lo viejo de la BD.
            $producto->nombre = $this->input->post('nombre');
            $producto->precio = $this->input->post('precio');
            $producto->stock = $this->input->post('stock');
        }

        $this->load->view('templates/header', ['titulo' => 'Editar producto']);
        $this->load->view('productos/editar', ['producto' => $producto]);
        $this->load->view('templates/footer');
    }

    public function eliminar($id)
    {
        $this->Producto_model->eliminar($id);
        $this->session->set_flashdata('mensaje', 'Producto eliminado correctamente.');
        redirect('productos');
    }
}
