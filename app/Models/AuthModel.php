<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;
use Exception;
use Throwable;

class AuthModel extends Model
{
    protected $table = 'tb_users';

    public function getUser($where = "")
    {
        try {
            if (empty($where)) {
                throw new Exception("Required data not found.", 400);
            }

            return $this->db->table("tb_users")
                ->where($where)
                ->select("*")
                ->get(1)->getRowArray();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function editUser($data, $where)
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_users");
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
            return false;
        }
    }

}
