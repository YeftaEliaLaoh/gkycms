<?php

class Admin_Model extends CI_Model 
{

	public function updateSettings($data) 
	{
		$this->db->where("ID", 1)->update("site_settings", $data);
	}

	public function add_ipblock($ip, $reason) 
	{
		$this->db->insert("ip_block", array(
			"IP" => $ip,
			"reason" => $reason,
			"timestamp" => time()
			)
		);
	}

	public function get_ip_blocks() 
	{
		return $this->db->get("ip_block");
	}

	public function get_ip_block($id) 
	{
		return $this->db->where("ID", $id)->get("ip_block");
	}

	public function delete_ipblock($id) {
		$this->db->where("ID", $id)->delete("ip_block");
	}

	public function get_email_template($id) 
	{
		return $this->db->where("ID", $id)->get("email_templates");
	}

	public function add_email_template($data) 
	{
		$this->db->insert("email_templates", $data);
	}

	public function update_email_template($id, $data) 
	{
		$this->db->where("ID", $id)->update("email_templates", $data);
	}

	public function delete_email_template($id) 
	{
		$this->db->where("ID", $id)->delete("email_templates");
	}
	
	public function get_user_groups() 
	{
		return $this->db->get("user_groups");
	}

	public function add_group($data) 
	{
		$this->db->insert("user_groups", $data);
	}

	public function get_user_group($id) 
	{
		return $this->db->where("ID", $id)->get("user_groups");
	}

	public function delete_group($id) {
		$this->db->where("ID", $id)->delete("user_groups");
	}

	public function delete_users_from_group($id) 
	{
		$this->db->where("groupid", $id)->delete("user_group_users");
	}

	public function update_group($id, $data) 
	{
		$this->db->where("ID", $id)->update("user_groups", $data);
	}

	public function get_users_from_groups($id, $page) 
	{
		return $this->db->where("user_group_users.groupid", $id)
			->select("users.ID as userid, users.username, user_groups.name, 
				user_groups.ID as groupid, user_groups.default")
			->join("users", "users.ID = user_group_users.userid")
			->join("user_groups", "user_groups.ID = user_group_users.groupid")
			->limit(20, $page)
			->get("user_group_users");
	}

	public function get_all_group_users($id) 
	{
		return $this->db->where("user_group_users.groupid", $id)
			->select("users.ID as userid, users.email, users.username, 
				user_groups.name, user_groups.ID as groupid, 
				user_groups.default")
			->join("users", "users.ID = user_group_users.userid")
			->join("user_groups", "user_groups.ID = user_group_users.groupid")
			->get("user_group_users");
	}

	public function get_total_user_group_members_count($groupid) 
	{
		$s= $this->db->where("groupid", $groupid)
			->select("COUNT(*) as num")
			->join("users", "users.ID = user_group_users.userid")
			->join("user_groups", "user_groups.ID = user_group_users.groupid")
			->get("user_group_users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_user_from_group($userid, $id) 
	{
		return $this->db->where("userid", $userid)
			->where("groupid", $id)->get("user_group_users");
	}

	public function delete_user_from_group($userid, $id) 
	{
		$this->db->where("userid", $userid)
			->where("groupid", $id)->delete("user_group_users");
	}

	public function delete_user_from_all_groups($userid) 
	{
		$this->db->where("userid", $userid)->delete("user_group_users");
	}

	public function add_user_to_group($userid, $id) 
	{
		$this->db->insert("user_group_users", 
			array(
			"userid" => $userid, 
			"groupid" => $id
			)
		);
	}

	public function get_all_users() 
	{
		return $this->db->select("users.email, users.ID as userid")
			->get("users");
	}

	public function add_payment_plan($data) 
	{
		$this->db->insert("payment_plans", $data);
	}

	public function get_payment_plans() 
	{
		return $this->db->get("payment_plans");
	}

	public function get_payment_plan($id) 
	{
		return $this->db->where("ID", $id)->get("payment_plans");
	}

	public function delete_payment_plan($id) 
	{
		$this->db->where("ID", $id)->delete("payment_plans");
	}

	public function update_payment_plan($id, $data)
	{
		$this->db->where("ID", $id)->update("payment_plans", $data);
	}

	public function get_payment_logs($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"payment_logs.email"
			)
		);
		return $this->db->select("users.ID as userid, users.username, users.email,
			users.avatar, users.online_timestamp,
			payment_logs.email, payment_logs.amount, payment_logs.timestamp, 
			payment_logs.ID, payment_logs.processor")
			->join("users", "users.ID = payment_logs.userid")
			->limit($datatable->length, $datatable->start)
			->get("payment_logs");
	}

	public function get_total_payment_logs_count() 
	{
		$s= $this->db
			->select("COUNT(*) as num")->get("payment_logs");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_user_roles() 
	{
		return $this->db->get("user_roles");
	}

	public function add_user_role($data) 
	{
		$this->db->insert("user_roles", $data);
	}

	public function get_user_role($id) 
	{
		return $this->db->where("ID", $id)->get("user_roles");
	}

	public function update_user_role($id, $data) 
	{
		$this->db->where("ID", $id)->update("user_roles", $data);
	}

	public function delete_user_role($id) 
	{
		$this->db->where("ID", $id)->delete("user_roles");
	}

	public function get_premium_users($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.email",
			"payment_plans.name"
			)
		);
		
		return $this->db->select("users.username, users.email, users.avatar,
		users.online_timestamp, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider, 
			payment_plans.name, users.premium_time")
		->join("payment_plans", "payment_plans.ID = users.premium_planid")
		->where("users.premium_time >", time())
		->or_where("users.premium_time", -1)
		->limit($datatable->length, $datatable->start)
		->get("users");
	}

	public function get_total_premium_users_count() 
	{
		$s= $this->db
			->select("COUNT(*) as num")->where("premium_time >", time())
			->or_where("users.premium_time", -1)
			->join("payment_plans", "payment_plans.ID = users.premium_planid")
			->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function add_custom_field($data) 
	{
		$this->db->insert("custom_fields", $data);
	}

	public function get_custom_fields($data) 
	{
		if(isset($data['register'])) {
			$this->db->where("register", 1);
		}
		return $this->db->get("custom_fields");
	}

	public function get_custom_field($id) 
	{
		return $this->db->where("ID", $id)->get("custom_fields");
	}

	public function update_custom_field($id, $data) 
	{
		return $this->db->where("ID", $id)->update("custom_fields", $data);
	}

	public function delete_custom_field($id) 
	{
		$this->db->where("ID", $id)->delete("custom_fields");
	}

	public function get_layouts() 
	{
		return $this->db->get("site_layouts");
	}

	public function get_layout($id) 
	{
		return $this->db->where("ID", $id)->get("site_layouts");
	}

	public function get_user_role_permissions() 
	{
		return $this->db->get("user_role_permissions");
	}

	public function get_total_email_templates() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("email_templates");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_email_templates($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"email_templates.title",
			"email_templates.language"
			)
		);
		
		return $this->db
		->limit($datatable->length, $datatable->start)
		->get("email_templates");
	}

	public function add_page_category($data) 
	{
		$this->db->insert("page_categories", $data);
	}

	public function add_themes($data) 
	{
		$this->db->insert("themes", $data);
	}
	public function update_themes($id, $data) 
	{
		$this->db->where("id", $id)->update("themes", $data);
	}
	public function get_page_themes($id) 
	{
		return $this->db->where("id", $id)->get("themes");
	}

	public function get_page_category($id) 
	{
		return $this->db->where("ID", $id)->get("page_categories");
	}

	public function delete_page_category($id) 
	{
		$this->db->where("ID", $id)->delete("page_categories");
	}

	public function update_page_category($id, $data) 
	{
		$this->db->where("ID", $id)->update("page_categories", $data);
	}

	public function get_total_page_categories() 
	{
		return $this->db->from("page_categories")->count_all_results();
	}

	public function get_page_categories($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"page_categories.name",
			),
			true // Cache query
		);
		return $datatable->get("page_categories");
	}

	public function get_total_themes() 
	{
		return $this->db->from("themes")->count_all_results();
	}

	public function get_themes($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"themes.name",
			),
			true // Cache query
		);
		return $datatable->get("themes");
	}

	public function delete_report($id) 
	{
		$this->db->where("ID", $id)->delete("reports");
	}

	public function get_total_reports() 
	{
		return $this->db->from("reports")->count_all_results();
	}

	public function get_report($id) 
	{
		return $this->db->where("ID", $id)->get("reports");
	}

	public function get_reports($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"reports.reason",
			"users.username",
			"pages.name",
			"g.username"
			),
			true // Cache query
		);
		$this->db
			->select("users.username as reported_username,
				users.first_name as reported_first_name, users.last_name as reported_last_name,
				users.avatar as reported_avatar, 
				users.online_timestamp as reported_online_timestamp,
				pages.ID as pageid, pages.name as page_name, pages.slug,
				g.username, g.avatar, g.first_name, g.last_name, g.online_timestamp,
				reports.ID, reports.timestamp, reports.reason")
			->join("users", "users.ID = reports.userid", "left outer")
			->join("pages", "pages.ID = reports.pageid", "left outer")
			->join("users as g", "g.ID = reports.fromid");

		return $datatable->get("reports");
	}
}

?>