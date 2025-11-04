<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hrd_model extends CI_Model
{
    private $dbs;
    private $verify = [
        'masuk' => 'jamin',
        'pulang' => 'jamout'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->dbs = $this->load->database('hrd', TRUE);
    }

    public function get_employees($input)
    {
        $table = 'karyawan_data';
        $where = [];
        $wherein = [];
        $wherenotin = [];
        $raw = [
            "tgl_terima <= '" . date('Y-m-d') . "'",
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
                    if ($v !== 'start' && $v !== 'length') {
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
                                $start = $input['start'];
                                break;
                            case 'length':
                                $length = $input['length'];
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

        $response['rows'] = clone $data;
        $response['rows'] = $response['rows']->get($table)->num_rows();
        $response['data'] = $data->limit($length, $start)->get($table)->result();

        return $response;
    }

    private function getData($input, $table)
    {
        $where = [];
        $wherein = [];
        $wherenotin = [];
        $raw = [];
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

    public function attendance_insert($data)
    {
        $check = $this->getData(
            [
                'tanggal' => $data['tgl_absen'],
                'kar_id' => $data['karyawan_id'],
            ],
            'absensi_d'
        );

        if ($check) {
            $doing = $this->updateDataAttendance($check[0], $data);
        } else {
            $doing = $this->insertDataAttendance($data);
        }
        return $doing;
    }

    private function insertDataAttendance($data)
    {
        $employees = $this->get_employees(['kar_id' => $data['karyawan_id']]);
        if ($employees) {
            $employees = $employees['data'][0];
            $shift = $this->getData(
                [
                    'id_group' => $employees->group,
                    'raw' => [
                        "drtgl <= '{$data['tgl_absen']}' AND ketgl >= '{$data['tgl_absen']}'"
                    ]
                ]
            , 'karyawan_shift');
            // return $shift;
    
            if ($shift) {
                $shift = $shift[0];

                $dataInsert = [
                    'userid' => $employees->kar_id,
                    'kar_id' => $employees->kar_id,
                    'nik' => $employees->nik,
                    'nama' => $employees->nama,
                    'divisi' => $employees->divisi,
                    'subdivisi' => $employees->subdivisi,
                    'tanggal' => $data['tgl_absen'],
                    'ip' => $data['cloud_id'],
                    'statusabsen' => 'Hadir',
                    'validasi' => '0',
                    'validasi_a' => '0',
                    'del' => '0',
                    'group' => $employees->group,
                    'shift' => $shift->id_shift,
                    'lokasi' => '1'
                ];

                $dataInsert[$this->verify[$data['status']]] = $data['jam'];

                return $this->dbs->insert('absensi_d', $dataInsert);
            }
        }

        return false;
    }

    private function updateDataAttendance($attendance, $data)
    {
        $updateData = [
            'statusabsen' => 'Hadir',
            "{$this->verify[$data['status']]}" => $data['jam']
        ];

        return $this->dbs->where('idabsensi', $attendance->idabsensi)->update('absensi_d', $updateData);
    }
}
