<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;

class RevisionModel extends Model
{
    protected $table      = 'tb_reimbursement_revisions';
    protected $primaryKey = 'rrev_id';

    private $mainColumnOrder = ['rrev_id'];
    private $mainColumnSearch = ['rrev_note'];
    private $mainOrder = ['rrev_id' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("rrev");
        return $key;
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
            ->selectMax("rrev_id", "last");
        $data = $builder->get(1)->getRowArray()["last"];
        return $data;
    }

    public function isUsed($reimId)
    {
        if ($this->db->table("tb_reimbursements")->where("reim_id", $reimId)->countAllResults() > 0) {
            return [
                "is_used" => true,
                "by" => "reimbursement",
            ];
        }
        return [
            "is_used" => false,
            "by" => "",
        ];
    }

    public function get($where = "", $row = false)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*");
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
        $builder = $this->db->table($this->table);
        if ($builder->insert($data)) {
            return $this->db->insertID();
        } else {
            return false;
        }
    }
}
