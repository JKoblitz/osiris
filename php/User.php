<?php


include_once 'Database.php';

class User extends Database
{
    public $user = "unknown";
    public $name = "";
    public $dept = "";
    private $info = [
        "is_admin" => 0,
        "is_controlling" => 0,
        "is_leader" => 0,
        "is_scientist" => 0,
        "is_active" => 0
    ];
    function __construct($user = null)
    {
        parent::__construct();
        if ($user !== null) {
            $this->user = $user;
            $stmt = $this->db->prepare(
                "SELECT * FROM users WHERE `user` LIKE ?"
            );
            $stmt->execute([$this->user]);
            $this->info = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->name = $this->info['first_name'] . " " . $this->info['last_name'];
            $this->dept = $this->info['dept'];
        }
    }

    function name($type = 'standard')
    {
        switch ($type) {
            case 'standard':
                return $this->name;
            case 'formal':
                return $this->info['last_name'] . ", " . $this->info['first_name'];
            case 'abbreviated':
                $fn = "";
                foreach (explode(" ",$this->info['first_name']) as $name) {
                    $fn .= " ".$name[0].".";
                }
                return $this->info['last_name'] . "," . $fn;
            default:
                # code...
                break;
        }
    }

    function is_admin()
    {
        return $this->info['is_admin'] == 1;
    }
    function is_controlling()
    {
        return $this->info['is_controlling'] == 1;
    }
    function is_leader()
    {
        return $this->info['is_leader'] == 1;
    }
    function is_scientist()
    {
        return $this->info['is_scientist'] == 1;
    }
    function is_active()
    {
        return $this->info['is_active'] == 1;
    }
}
