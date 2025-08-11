<?php

namespace App\Models;

use App\Libraries\RowKeyGeneratorv2;
use CodeIgniter\Model;
use Exception;
use Firebase\JWT\JWT;

class UserSessionModel extends Model
{
    protected $table = 'tb_user_sessions';

    private $mainColOrder = ['usr_key'];
    private $mainColSearch = ['usr_email'];
    private $mainOrder = ['usr_created_at' => 'desc'];

    public function generateKey()
    {
        $key = RowKeyGeneratorv2::generate("us");
        return $key;
    }

    public function generateToken($usrKey, $userSessionKey, int $expired)
    {
        try {
            // JWT payload for user session token
            // "iss"  => Issuer of the token (here: "kepegawaian")
            // "aud"  => Audience for whom the token is intended (here: "kepegawaian")
            // "sub"  => Subject of the token (here: "user_session")
            // "us_key" => Unique key for the user session
            // "usr_key" => Unique key for the user
            // "iat"  => Issued At: timestamp when the token is generated
            // "nbf"  => Not Before: token is not valid before this timestamp
            // "exp"  => Expiration: timestamp when the token expires (current time + authLifetime())
            log_message("alert", "gentoken:" . $expired . "|" . date("Y-m-d H:i:s", $expired));
            $jwtPayload = [
                "iss" => "reimbursement",
                "aud" => "reimbursement",
                "sub" => "user_session",
                "us_key" => $userSessionKey,
                "usr_key" => $usrKey,
                "device" => appDeviceInfo(),
                "iat" => time(),
                "nbf" => time(),
                "exp" => $expired,
            ];
            $token = JWT::encode($jwtPayload, authJWTSecretKey(), authJWTAlgo());
            return $token;
        } catch (\Throwable $th) {
            appSaveThrowable($th);
            return null;
        }
    }

    private function _dtblQuery($conditions = [])
    {
        $builder = $this->db->table("tb_user_sessions");
        $builder->join("tb_users", "tb_users.usr_id = tb_user_sessions.us_usr_id");
        $builder->select("tb_user_sessions.*");

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
        $builder = $this->db->table("tb_user_sessions");
        $builder->join("tb_users", "tb_users.usr_id = tb_user_sessions.us_usr_id");
        if (isset($conditions["where"])) {
            foreach ($conditions["where"] as $key => $value) {
                $builder->where($key, $value);
            }
        }

        $builder->select("tb_user_sessions.*");

        $data = $builder->countAllResults();
        // log_message("error", $this->db->getLastQuery());
        return $data;
    }

    public function add($data)
    {
        try {
            $this->db->transStart();
            $builder = $this->db->table("tb_user_sessions");
            $builder->insert($data);
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                throw new Exception("Insert user failed.");
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
            $builder = $this->db->table("tb_user_sessions");
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
            $builder = $this->db->table("tb_user_sessions");
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
        $builder = $this->db->table("tb_user_sessions")
            ->join("tb_users", "tb_users.usr_id = tb_user_sessions.us_usr_id")
            ->join("tb_group_user", "tb_group_user.group_id = tb_users.usr_group_id", "left");

        if (!empty($where)) {
            $builder->where($where);
        }
        $builder->select("tb_user_sessions.*");
        $builder->select("tb_users.usr_is_active,tb_users.usr_key,tb_users.usr_email,tb_users.usr_username,tb_users.usr_id,tb_users.usr_role,tb_users.usr_photo_file_name");
        $builder->select("tb_group_user.group_id,tb_group_user.group_name");
        if ($single) {
            $data = $builder->get(1)->getRowArray();
        } else {
            $data = $builder->get()->getResultArray();
        }
        return $data;
    }
}
