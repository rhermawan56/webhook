<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Api extends CI_Controller
{

    private $key;
    private $fingertoken;
    private $fingerurl = "https://developer.fingerspot.io/api/";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'u');
        $this->load->model('Hrd_model', 'hr');
        $this->load->helper(['url', 'form']);
        $this->load->library('curl');
        $this->key = getenv('JWT_KEY');
        $this->fingertoken = getenv('F_TOKEN');
    }

    public function login()
    {
        $input = json_decode(trim(file_get_contents('php://input')), true);

        $username = $input['username'] ? $input['username'] : '';
        $password = $input['password'] ? $input['password'] : '';

        $user = $this->u->get_user($username, $password);

        if ($user) {
            $payload = [
                'iss' => 'http://localhost',
                'aud' => 'http://localhost',
                'iat' => time(),
                'exp' => time() + (60 * 60),
                'data' => [
                    'username' => $user
                ]
            ];

            $token = JWT::encode($payload, $this->key, 'HS256');

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'token' => $token]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Login gagal']));
        }
    }

    private function headerAuth()
    {
        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization'])) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Token tidak ditemukan']));
            return;
        }

        $authHeader = $headers['Authorization'];
        return trim(str_replace('Bearer', '', $authHeader));
    }

    private function fingerspot($data) {
        $headers = [
            "Authorization: Bearer " . $this->fingertoken
        ];
        $response = $this->curl->simple_post($this->fingerurl, $data, $headers);

        return $response;
    }

    public function get_attlog()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'get_attlog';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function get_userinfo()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'get_userinfo';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function get_all_pin()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'get_all_pin';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }
    
    public function reg_online()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'reg_online';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function set_time()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'set_time';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function restart_device()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'restart_device';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function get_device()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        $this->fingerurl .= 'get_device';

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->fingerspot($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }

    public function get_employees()
    {
        $token = $this->headerAuth();
        $input = json_decode(trim(file_get_contents('php://input')), true);
        // print_r($input);die;

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $user = $decoded->data;
            $data = $this->hr->get_employees($input);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true, 'data' => $data]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $e->getMessage()]));
        }
    }
}
