<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;

class BerkasModel extends Model
{
    protected $table      = 'tb_reimbursement_berkas';
    protected $primaryKey = 'rb_id';

    private $mainColumnOrder = ['rb_id'];
    private $mainColumnSearch = ['rb_file_name', 'rb_file_name_origin'];
    private $mainOrder = ['rb_id' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("rb");
        return $key;
    }

    public function generateCode()
    {
        $prefix = "RB";
        $code = $prefix . "" . strtoupper(appGenerateRandomString(3)) . "" . appGenerateRandomNumber(3);
        $builder = $this->db->table("tb_reimbursement_berkas");
        $builder->select("rb_code");
        $builder->where("rb_code", $code);
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
            ->selectMax("rb_id", "last");
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
        $builder->join("tb_jenis_berkas", "tb_jenis_berkas.jb_id = " . $this->table . ".rb_jb_id", "left");
        $builder->join("tb_reimbursements", "tb_reimbursements.reim_id = " . $this->table . ".rb_reim_id");
        $builder->join("tb_users as uclaimant", "uclaimant.usr_id = tb_reimbursements.reim_claimant_usr_id");
        $builder->select($this->table . ".*");
        $builder->select("tb_jenis_berkas.*");
        $builder->select("tb_reimbursements.reim_triwulan_no,tb_reimbursements.reim_triwulan_tahun,tb_reimbursements.reim_claimant_usr_id");
        $builder->select("uclaimant.usr_key as uc_usr_key");
        if ($where != "") {
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
}
