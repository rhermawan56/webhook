<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_test extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('curl');
    }

    public function index()
    {
        $url = "https://developer.fingerspot.io/api/get_attlog";
        $token = "X8MUFRSANOEY1D2Q";

        $data = [
            "trans_id" => "2",
            "cloud_id" => "C263388123302938",
            "start_date" => "2025-10-30",
            "end_date" => "2025-10-30"
        ];

        $headers = [
            "Authorization: Bearer " . $token
        ];

        $response = $this->curl->simple_post($url, $data, $headers);

        echo "<pre>";

        print_r($response);
    }
}
