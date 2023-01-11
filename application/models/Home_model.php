<?php

class Home_Model extends CI_Model 
{

	public function get_home_stats() 
	{
		return $this->db->get("home_stats");
	}

	public function update_home_stats($stats) 
	{
		$this->db->where("ID", 1)->update("home_stats", array(
			"google_members" => $stats->google_members,
			"facebook_members" => $stats->facebook_members,
			"twitter_members" => $stats->twitter_members,
			"total_members" => $stats->total_members,
			"new_members" => $stats->new_members,
			"active_today" => $stats->active_today,
			"timestamp" => time()
			)
		);
	}

	public function provinces($id) 
	{
		if(!empty($id)) {
			return $this->db->where("ID", $id)->get("provinces");
		} else {
			return $this->db->get("provinces");
		}
		
	}

	public function themes($id) 
	{
		if(!empty($id)) {
			return $this->db->where("id", $id)->get("themes");
		} else {
			return $this->db->get("themes");
		}
		
	}

	public function city($id) 
	{

		return $this->db->where("province_id", $id)->get("city");
	}

	public function subdistrict($id) 
	{

		return $this->db->where("city_id", $id)->get("subdistrict");
	}

	public function get_email_template($id) 
	{
		return $this->db->where("ID", $id)->get("email_templates");
	}

	public function get_email_template_hook($hook, $language) 
	{
		return $this->db->where("hook", $hook)
			->where("language", $language)->get("email_templates");
	}

}

?>