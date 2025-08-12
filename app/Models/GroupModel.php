<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;
use Exception;

class GroupModel extends Model
{
    protected $table = 'tb_group_user';

    private $mainColOrder = ['group_key'];
    private $mainColSearch = ['group_name'];
    private $mainOrder = ['group_id' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("gu");
        return $key;
    }

    public function generateCode()
    {
        $prefix = "GU";
        $code = $prefix . "" . strtoupper(appGenerateRandomString(3)) . "" . appGenerateRandomNumber(3);
        $builder = $this->db->table("tb_group_user");
        $builder->select("group_code");
        $builder->where("group_code", $code);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $this->generateCode(); // recursive call to ensure unique code
        }
        return $code;
    }

    private function _dtblQuery($conditions = [])
    {
        $builder = $this->db->table("tb_group_user");
        $builder->join("tb_users", "tb_users.usr_id = tb_group_user.group_admin_usr_id", "left");
        $builder->select("tb_group_user.*");
        $builder->select("tb_users.usr_username");

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
        $builder = $this->db->table("tb_group_user");
        $builder->join("tb_users", "tb_users.usr_id = tb_group_user.group_admin_usr_id", "left");
        $builder->select("tb_group_user.*");
        $builder->select("tb_users.usr_username");
        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }
        $builder->select("tb_group_user.*");
        $data = $builder->countAllResults();
        return $data;
    }

    public function add($data)
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_group_user");
            $builder->insert($data);
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                throw new Exception("Insert failed.");
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
            $builder = $this->db->table("tb_group_user");
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
            $builder = $this->db->table("tb_group_user");
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
        $builder = $this->db->table("tb_group_user");

        // Join ke admin user
        $builder->join("tb_users", "tb_users.usr_id = tb_group_user.group_admin_usr_id", "left");

        // Join ke leader user dengan alias + kondisi tambahan
        $builder->join(
            "tb_users as uleader",
            "uleader.usr_id = tb_group_user.group_leader_usr_id AND uleader.usr_group_category = 'ketua'",
            "left"
        );

        // Select kolom yang dibutuhkan
        $builder->select("tb_group_user.*, tb_users.usr_username");
        $builder->select("uleader.usr_username as leader_username");

        // Filter where jika ada
        if (!empty($where)) {
            $builder->where($where);
        }

        // Ambil hasil
        $query = $single ? $builder->get(1)->getRowArray() : $builder->get()->getResultArray();

        // Log query untuk debug
        // log_message("error", "get group: " . $this->db->getLastQuery());

        return $query;
    }


    public function getUsers($groupId)
    {
        try {
            $builder = $this->db->table("tb_users");
            $builder->join("tb_group_user", "tb_group_user.group_id = tb_users.usr_group_id");
            $builder->where("tb_group_user.group_id", $groupId);
            $builder->select("tb_users.*");
            $builder->select("tb_group_user.group_name");
            $data = $builder->get()->getResultArray();
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
