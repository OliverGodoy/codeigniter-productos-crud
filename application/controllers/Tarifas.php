<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarifas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tarifa_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['tarifas'] = $this->Tarifa_model->obtener_todos();
        $this->load->view('templates/header', ['titulo' => 'Tarifas']);
        $this->load->view('tarifas/index', $data);
        $this->load->view('templates/footer');
    }

    public function crear()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
            $this->form_validation->set_rules('precio_por_metro_cubico', 'Precio por metro cúbico', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('consumo_minimo', 'Consumo mínimo', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('activa', 'Activo', 'required|in_list[0,1]');


            if ($this->form_validation->run()) {
                $this->Tarifa_model->crear([
                    'nombre' => $this->input->post('nombre'),
                    'precio_por_metro_cubico' => $this->input->post('precio_por_metro_cubico'),
                    'consumo_minimo' => $this->input->post('consumo_minimo'),
                    'activa' => $this->input->post('activa'),

                ]);

                $this->session->set_flashdata('mensaje', 'Tarifa creada correctamente.');
                redirect('tarifas');
                return;
            }
        }

        $this->load->view('templates/header', ['titulo' => 'Nueva tarifa']);
        $this->load->view('tarifas/crear');
        $this->load->view('templates/footer');
    }

    public function editar($id)
    {
        $tarifa = $this->Tarifa_model->obtener($id);

        if (!$tarifa) {
            show_404();
            return;
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
            $this->form_validation->set_rules('precio_por_metro_cubico', 'Precio por metro cúbico', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('consumo_minimo', 'Consumo mínimo', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('activa', 'Activo', 'required|in_list[0,1]');

            if ($this->form_validation->run()) {
                $this->Tarifa_model->actualizar($id, [
                    'nombre' => $this->input->post('nombre'),
                    'precio_por_metro_cubico' => $this->input->post('precio_por_metro_cubico'),
                    'consumo_minimo' => $this->input->post('consumo_minimo'),
                    'activa' => $this->input->post('activa'),
                ]);

                $this->session->set_flashdata('mensaje', 'Tarifa actualizada correctamente.');
                redirect('tarifas');
                return;
            }
        }

        $data['tarifa'] = $tarifa;
        $this->load->view('templates/header', ['titulo' => 'Editar tarifa']);
        $this->load->view('tarifas/editar', $data);
        $this->load->view('templates/footer');
    }

    public function eliminar($id)
    {
        $tarifa = $this->Tarifa_model->obtener($id);

        if (!$tarifa) {
            show_404();
            return;
        }

        $this->Tarifa_model->eliminar($id);
        $this->session->set_flashdata('mensaje', 'Tarifa eliminada correctamente.');
        redirect('tarifas');
    }
}