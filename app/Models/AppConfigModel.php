<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;
use Exception;

class AppConfigModel extends Model
{
    protected $table = 'tb_configs';

    private $mainColOrder = ['cfg_key'];
    private $mainColSearch = ['cfg_name'];
    private $mainOrder = ['cfg_updated_at' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("ac");
        return $key;
    }

    private function _dtblQuery($conditions = [])
    {
        $builder = $this->db->table("tb_configs");
        $builder->select("tb_configs.*");

        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $i = 0;
        foreach ($this->mainColSearch as $ev) {
            if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                $_POST['search']['value'] = $_POST['search']['value'];
            } else {
                $_POST['search']['value'] = '';
            }
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) {
                    $builder->groupStart();
                    $builder->like($ev, $_POST['search']['value']);
                } else {
                    $builder->orLike($ev, $_POST['search']['value']);
                }
                if (count($this->mainColSearch) - 1 == $i) { // last loop
                    $builder->groupEnd(); // close bracket
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $builder->orderBy($this->mainColOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->mainOrder)) {
            $order = $this->mainOrder;
            $builder->orderBy(key($order), $order[key($order)]);
        }
        // log_message("error", $this->db->getLastQuery());
        return $builder;
    }

    function getDtbl($conditions = [])
    {
        $builder = $this->_dtblQuery($conditions);

        if (isset($_POST['length']) && $_POST['length'] < 1) {
            $_POST['length'] = '10';
        } elseif (isset($_POST['length']) && $_POST['length'] > 1) {
            $_POST['length'] = $_POST['length'];
        } else {
            $_POST['length'] = 10;
        }

        if (isset($_POST['start']) && $_POST['start'] > 1) {
            $_POST['start'] = $_POST['start'];
        } else {
            $_POST['start'] = 0;
        }
        $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        $data = $query->getResultArray();
        return $data;
    }

    function dTblCountFiltered($conditions = [])
    {
        $builder = $this->_dtblQuery($conditions);
        $data = $builder->countAllResults();
        return $data;
    }

    function dTblCountAll($conditions = [])
    {
        $builder = $this->db->table("tb_configs");
        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->select("tb_configs.*");

        $data = $builder->countAllResults();
        // log_message("error", $this->db->getLastQuery());
        return $data;
    }

    public function add($data)
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_configs");
            $builder->insert($data);
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                throw new Exception("Insert config failed.");
            }
            return true;
        } catch (\Throwable $th) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
            appSaveThrowable($th);
            return false;
        }
    }

    public function edit($data, $where)
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_configs");
            $builder->where($where);
            if ($builder->update($data)) {
                $this->db->transComplete();
            } else {
                throw new Exception("Update failed.", 400);
            }

            if ($this->db->transStatus() === false) {
                throw new Exception("update failed.");
            }
            return true;
        } catch (\Throwable $th) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
            appSaveThrowable($th);
            return false;
        }
    }

    public function del($where = "")
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_configs");
            $builder->delete($where);
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                throw new Exception("delete failed.");
            }
            return true;
        } catch (\Throwable $th) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
            appSaveThrowable($th);
            return false;
        }
    }

    public function get($where = "", $single = false)
    {
        $builder = $this->db->table("tb_configs");
        if (!empty($where)) {
            $builder->where($where);
        }
        $builder->select("tb_configs.*");
        if ($single) {
            $data = $builder->get(1)->getRowArray();
        } else {
            $data = $builder->get()->getResultArray();
        }
        return $data;
    }
}
