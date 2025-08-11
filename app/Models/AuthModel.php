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

    /**
     * Access Control List (ACL) for user roles
     */
    public function getACL($accessResource = "")
    {
        try {
            $list = [
                "r_user" => ["super_user", "admin_group", "admin_validator"],
                "c_user" => ["super_user"],
                "u_user" => ["super_user"],
                "d_user" => ["super_user"],

            ];
            if (!empty($accessResource)) {
                if (array_key_exists($accessResource, $list)) {
                    return $list[$accessResource];
                } else {
                    throw new Exception("Access resource not found.", 404);
                }
            } else {
                return $list;
            }
        } catch (Throwable $th) {
            log_message('error', 'Error in getACL: [ ' . $th->getLine() . " ] " . $th->getMessage());
            return null;
        }
    }
}
