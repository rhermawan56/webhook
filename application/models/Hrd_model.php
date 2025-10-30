<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hrd_model extends CI_Model {
    private $dbs;

    public function __construct() {
        parent::__construct();
        $this->dbs = $this->load->database('hrd', TRUE);
    }

    public function get_employees($input) {
        $table = 'karyawan_data';
        $where = [];
        $wherein = [];
        $wherenotin = [];
        $raw = [
            "tgl_terima <= '".date('Y-m-d')."'",
            "statuskaryawan <> 'Keluar'",
            "del = '0'",
            "tgl_keluar = '0000-00-00'" 
        ];
        $start = 0;
        $length = 100;
        $exclude = ['wherein', 'wherenotin', 'raw', 'start', 'length'];

        $data = $this->dbs;

        if ($input) {
            $dataKeys = array_keys($input);

            foreach ($dataKeys as $k => $v) {
                if (!in_array($v, $exclude)) {
                    $where[$v] = $input[$v];
                } else {
                    // print_r($v);
                    if ($v !== 'start' && $v !== 'end') {
                        foreach ($input[$v] as $kw => $vw) {
                            switch ($v) {
                                case 'wherein':
                                    if ($vw['values']) {
                                        $wherein[] = [
                                            'field' => $vw['field'],
                                            'values' => $vw['values']
                                        ];
                                    }
                                    break;

                                case 'wherenotin':
                                    if ($vw['values']) {
                                        $wherenotin[] = [
                                            'field' => $vw['field'],
                                            'values' => $vw['values']
                                        ];
                                    }
                                    break;

                                case 'raw':
                                    $raw[] = $vw;
                                    break;
                            }
                        }
                    } else {
                        switch ($v) {
                            case 'start':
                                $start = $input('start');
                                break;
                            case 'length':
                                $length = $input('end');
                                break;
                        }
                    }
                }
            }

            if ($where) {
                $data = $data->where($where);
            }

            if ($wherein) {
                foreach ($wherein as $k => $v) {
                    $data = $data->where_in($v['field'], $v['values']);
                }
            }

            if ($wherenotin) {
                foreach ($wherenotin as $k => $v) {
                    $data = $data->where_not_in($v['field'], $v['values']);
                }
            }

            if ($raw) {
                foreach ($raw as $k => $v) {
                    $data = $data->where("{$v}", null, false);
                }
            }
        } else {
            if ($raw) {
                foreach ($raw as $k => $v) {
                    $data = $data->where("{$v}", null, false);
                }
            }
        }

        $data = $data->limit($length, $start)->get($table)->result();

        return $data;
    }
}
