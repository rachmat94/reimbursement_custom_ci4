<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;

class ReimbursementModel extends Model
{
    protected $table      = 'tb_reimbursements';
    protected $primaryKey = 'reim_id';

    private $mainColumnOrder = ['reim_id'];
    private $mainColumnSearch = ['reim_code'];
    private $mainOrder = ['reim_id' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("reim");
        return $key;
    }

    public function generateCode()
    {
        $prefix = "REIM";
        $code = $prefix . "" . strtoupper(appGenerateRandomString(3)) . "" . appGenerateRandomNumber(3);
        $builder = $this->db->table("tb_reimbursements");
        $builder->select("reim_code");
        $builder->where("reim_code", $code);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $this->generateCode(); // recursive call to ensure unique code
        }
        return $code;
    }

    private function _dtblQuery($conditions = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*");

        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $i = 0;
        foreach ($this->mainColumnSearch as $ev) {
            if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                $_POST['search']['value'] = $_POST['search']['value'];
            } else {
                $_POST['search']['value'] = '';
            }
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $builder->groupStart();
                    $builder->like($ev, $_POST['search']['value']);
                } else {
                    $builder->orLike($ev, $_POST['search']['value']);
                }
                if (count($this->mainColumnSearch) - 1 == $i) {
                    $builder->groupEnd();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $builder->orderBy($this->mainColumnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->mainOrder)) {
            $order = $this->mainOrder;
            $builder->orderBy(key($order), $order[key($order)]);
        }
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
        return $query->getResultArray();
    }

    function dTblCountFiltered($conditions = [])
    {
        $builder = $this->_dtblQuery($conditions);
        return $builder->countAllResults();
    }

    function dTblCountAll($conditions = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*");
        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }
        return $builder->countAllResults();
    }

    public function lastId()
    {
        $builder = $this->db->table($this->table)
            ->selectMax("reim_id", "last");
        $data = $builder->get(1)->getRowArray()["last"];
        return $data;
    }

    public function isUsed($categoryId)
    {
        return [
            "is_used" => false,
            "by" => "",
        ];
    }

    public function get($where = "", $row = false)
    {
        $builder = $this->db->table($this->table);
        $builder->join("tb_categories", "tb_categories.cat_id = " . $this->table . ".reim_cat_id", "left");
        $builder->join("tb_submission_window", "tb_submission_window.sw_id = " . $this->table . ".reim_sw_id", "left");
        $builder->join("tb_users as uby", "uby.usr_id = " . $this->table . ".reim_by_usr_id", "left");
        $builder->join("tb_users as uclaimant", "uclaimant.usr_id = " . $this->table . ".reim_claimant_usr_id", "left");
        $builder->join("tb_users as udiajukan", "udiajukan.usr_id = " . $this->table . ".reim_diajukan_by_usr_id", "left");
        $builder->join("tb_users as uvalidator", "uvalidator.usr_id = " . $this->table . ".reim_validation_by_usr_id", "left");
        $builder->join("tb_group_user as ucgroup", "ucgroup.group_id = uclaimant.usr_group_id", "left");
        $builder->select($this->table . ".*");
        $builder->select("tb_categories.cat_name,tb_categories.cat_code");
        $builder->select("tb_submission_window.sw_start_date,tb_submission_window.sw_end_date");
        $builder->select("uby.usr_id as uby_usr_id,uby.usr_code as uby_usr_code,uby.usr_username as uby_usr_username");
        $builder->select("uclaimant.usr_id as uc_usr_id,uclaimant.usr_key as uc_usr_key,uclaimant.usr_code as uc_usr_code,uclaimant.usr_username as uc_usr_username,uclaimant.usr_email as uc_usr_email,uclaimant.usr_group_id as uc_usr_group_id");
        $builder->select("udiajukan.usr_id as ud_user_id,udiajukan.usr_code as ud_usr_code,udiajukan.usr_username as ud_usr_username,udiajukan.usr_email as ud_usr_email");
        $builder->select("uvalidator.usr_id as uv_usr_id, uvalidator.usr_code as uv_usr_code, uvalidator.usr_username as uv_usr_username, uvalidator.usr_email as uv_usr_email");
        $builder->select("ucgroup.group_id as ucg_group_id, ucgroup.group_key as ucg_group_key,ucgroup.group_code as ucg_group_code,ucgroup.group_name as ucg_group_name");

        if (!empty($where)) {
            $builder->where($where);
        }
        if ($row) {
            $data = $builder->get(1)->getRowArray();
        } else {
            $data = $builder->get()->getResultArray();
        }
        return $data;
    }

    public function del($where = "")
    {
        if (empty($where)) {
            return false;
        }
        $builder = $this->db->table($this->table);
        $builder->delete($where);
        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($data, $where)
    {
        $builder = $this->db->table($this->table);
        $builder->where($where);
        return $builder->update($data);
    }

    public function add($data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            $this->db->transStart();

            $builder = $this->db->table($this->table);
            $success = $builder->insert($data);

            $insertID = $this->db->insertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false || !$success) {
                return false;
            }

            return $insertID;
        } catch (\Throwable $e) {
            // Jika terjadi error, rollback manual untuk memastikan
            $this->db->transRollback();

            // Kamu bisa log error-nya jika diperlukan:
            // log_message('error', $e->getMessage());

            return false;
        }
    }


    private $ufrColumnSearch = ["reim_code", "group_name", "usr_username", "usr_group_category", "usr_role", "reim_status","usr_email"];
    private $ufrColumnOrder = ["usr_id", "usr_id", "group_name", "usr_username", "usr_role", "usr_group_category", "reim_code", "reim_status", "cat_name", "reim_amount"];
    private $ufrMainOrder = ["reim_id" => "asc"];


    private function _dtblQueryUserForReim($conditions = [])
    {
        $builder = $this->db->table("tb_users");
        $builder->join("tb_group_user as tbgu", "tbgu.group_id = tb_users.usr_group_id", "left");
        $builder->join("tb_reimbursements as tbreim", "tbreim.reim_claimant_usr_id = tb_users.usr_id", "left");
        $builder->join("tb_categories", "tb_categories.cat_id = tbreim.reim_cat_id", "left");
        $builder->select("tb_users.usr_id,tb_users.usr_key,tb_users.usr_code,tb_users.usr_username,tb_users.usr_email,tb_users.usr_role,tb_users.usr_group_id,tb_users.usr_group_category,tb_users.usr_is_active");
        $builder->select("tbgu.group_id,tbgu.group_key,tbgu.group_code,tbgu.group_name,tbgu.group_admin_usr_id,tbgu.group_leader_usr_id");
        $builder->select("tbreim.reim_id,tbreim.reim_key,tbreim.reim_code,tbreim.reim_triwulan_no,tbreim.reim_triwulan_tahun,tbreim.reim_status,tbreim.reim_amount");
        $builder->select("tb_categories.cat_id,tb_categories.cat_code,tb_categories.cat_name,tb_categories.cat_key");
        $builder->select("CASE WHEN tbreim.reim_id IS NULL THEN 0 ELSE 1 END AS has_reimbursement", false);

        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->groupStart()
            ->where('tbreim.reim_id IS NULL')
            ->orGroupStart()
            ->where('tbreim.reim_triwulan_no', $conditions["triwulan"])
            ->where('tbreim.reim_triwulan_tahun', $conditions["tahun"])
            ->groupEnd()
            ->groupEnd();

        $i = 0;
        foreach ($this->ufrColumnSearch as $ev) {
            if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                $_POST['search']['value'] = $_POST['search']['value'];
            } else {
                $_POST['search']['value'] = '';
            }
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $builder->groupStart();
                    $builder->like($ev, $_POST['search']['value']);
                } else {
                    $builder->orLike($ev, $_POST['search']['value']);
                }
                if (count($this->ufrColumnSearch) - 1 == $i) {
                    $builder->groupEnd();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $builder->orderBy($this->ufrColumnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->ufrMainOrder)) {
            $order = $this->ufrMainOrder;
            $builder->orderBy(key($order), $order[key($order)]);
        }
        return $builder;
    }

    function getDtblUserForReim($conditions = [])
    {
        $builder = $this->_dtblQueryUserForReim($conditions);

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
        return $query->getResultArray();
    }

    function dTblCountFilteredUserForReim($conditions = [])
    {
        $builder = $this->_dtblQueryUserForReim($conditions);
        return $builder->countAllResults();
    }

    function dTblCountAllUserForReim($conditions = [])
    {
        $builder = $this->db->table("tb_users");
        $builder->join("tb_group_user as tbgu", "tbgu.group_id = tb_users.usr_group_id", "left");
        $builder->join("tb_reimbursements as tbreim", "tbreim.reim_claimant_usr_id = tb_users.usr_id", "left");
        $builder->join("tb_categories", "tb_categories.cat_id = tbreim.reim_cat_id", "left");
        $builder->select("tb_users.usr_id,tb_users.usr_key,tb_users.usr_code,tb_users.usr_username,tb_users.usr_email,tb_users.usr_role,tb_users.usr_group_id,tb_users.usr_group_category,tb_users.usr_is_active");
        $builder->select("tbgu.group_id,tbgu.group_key,tbgu.group_code,tbgu.group_name,tbgu.group_admin_usr_id,tbgu.group_leader_usr_id");
        $builder->select("tbreim.reim_id,tbreim.reim_key,tbreim.reim_code,tbreim.reim_triwulan_no,tbreim.reim_triwulan_tahun,tbreim.reim_status,tbreim.reim_amount");
        $builder->select("tb_categories.cat_id,tb_categories.cat_code,tb_categories.cat_name,tb_categories.cat_key");
        $builder->select("CASE WHEN tbreim.reim_id IS NULL THEN 0 ELSE 1 END AS has_reimbursement", false);

        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->groupStart()
            ->where('tbreim.reim_id IS NULL')
            ->orGroupStart()
            ->where('tbreim.reim_triwulan_no', $conditions["triwulan"])
            ->where('tbreim.reim_triwulan_tahun', $conditions["tahun"])
            ->groupEnd()
            ->groupEnd();
        return $builder->countAllResults();
    }
}
