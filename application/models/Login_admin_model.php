<?php

class Login_admin_Model extends CI_Model 
{

	public function getUser($email, $pass) 
	{
		return $this->db->select("ID")
		->where("email", $email)->where("password", $pass)->get("users");
	}

	public function getUserByEmail($email) 
	{
		/*
		return $this->db->select("*")
		->where("email", $email)->get("users");
		*/
		return $this->db->query("SELECT u.*,p.nama_provinsi as provinsi_name,k.nama_kabkota as kota_name,kc.nama_kecamatan as kecamatan_name,
		kl.`name` as kelurahan_name FROM users u 
		LEFT JOIN ref_provinsi p ON p.id=u.provinsi
		LEFT JOIN ref_kabkota k ON k.id=u.kota
		LEFT JOIN ref_kecamatan kc ON kc.id=u.kecamatan
		LEFT JOIN ref_kelurahan kl ON kl.id=u.kelurahan
		WHERE (u.email = '$email' OR u.username = '$email')");
	}

	public function getUserByUsername($username) 
	{
		/*
		return $this->db->select("*")
		->where("username", $username)->get("users");
		*/
		return $this->db->query("SELECT u.username as email FROM users u WHERE u.username = '$username'");
	}

	public function checkUserData($id) 
	{
		
		return $this->db->query("SELECT * FROM users u WHERE u.ID = '$id' AND u.email != '' AND u.phone != ''");
	}

	public function updateUserToken($userid, $token) 
	{
		$this->db->where("ID", $userid)
		->update("admin", array("token" => $token));
	}

	public function addToResetLog($ip) 
	{
		$this->db->insert("reset_log", 
			array(
				"IP" => $ip, 
				"timestamp" => time()
			)
		);
	}

	public function getResetLog($ip) 
	{
		return $this->db->where("IP", $ip)->get("reset_log");
	}

	public function getUserEmail($email) 
	{
		return $this->db->where("email", $email)
		->select("id, username")->get("admin");
	}

	public function resetPW($userid, $token) 
	{
		$this->db->insert("password_reset", 
			array(
				"userid" => $userid, 
				"token" => $token, 
				"IP" => $_SERVER['REMOTE_ADDR'], 
				"timestamp" => time()
			)
		);
	}

	public function getResetUser($token, $userid) 
	{
		return $this->db->where("token", $token)
		->where("id", $userid)->get("admin");
	}

	public function get_user_by_token($token) 
	{
		
		return $this->db->query("SELECT * FROM admin u WHERE u.token = '$token'");
	}

	public function updatePassword($userid, $password) 
	{
		$this->db->where("ID", $userid)
		->update("users", array("password" => $password));
	}

	public function updatetoken($userid,$token) 
	{
		$this->db->where("id", $userid)
		->update("admin", array("token" => $token));
	}

	public function updatedeviceid($userid, $deviceid) 
	{
		$this->db->where("ID", $userid)
		->update("admin", array("device_id" => $deviceid));
	}

	public function deleteReset($token) 
	{
		$this->db->where("token", $token)->delete("password_reset");
	}

	public function get_oauth_user($provider, $oauth_id) 
	{
		return $this->db->where("oauth_provider", $provider)
		->where("oauth_id", $oauth_id)
		->get("users");
	}

	public function update_facebook_user($provider, $oauth_id, $token) 
	{
		$this->db->where("oauth_id", $oauth_id)
		->where("oauth_provider", $provider)
		->update("users", array(
			"oauth_token" => $token,
			"IP" => $_SERVER['REMOTE_ADDR']
			)
		);
	}

	public function update_google_user($provider, $oauth_id, $token) 
	{
		$this->db->where("oauth_id", $oauth_id)
		->where("oauth_provider", $provider)
		->update("users", array(
			"oauth_token" => $token,
			"IP" => $_SERVER['REMOTE_ADDR']
			)
		);
	}

	public function update_oauth_user($oauth_token, $oauth_secret,
		$oauth_id, $provider) 
	{

		$this->db->where("oauth_id", $oauth_id)
		->where("oauth_provider", $provider)
		->update("users", array(
			"oauth_token" => $oauth_token,
			"oauth_secret" => $oauth_secret,
			"IP" => $_SERVER['REMOTE_ADDR']
			)
		);
	}

	public function get_login_attempts($ip, $username, $time) 
    {
    	return $this->db->where("IP", $ip)->where("username", $username)
    		->where("timestamp >", time() - $time)->get("login_attempts");
    }

    public function update_login_attempt($id, $data) 
    {
    	$this->db->where("ID", $id)->update("login_attempts", $data);
    }

    public function add_login_attempt($data) 
    {
    	$this->db->insert("login_attempts", $data);
    }

}

?>