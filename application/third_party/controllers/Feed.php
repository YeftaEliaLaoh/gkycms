<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("feed_model");
		$this->load->model("image_model");
		$this->load->model("page_model");

		if(!$this->user->loggedin) $this->template->error(lang("error_1"));

		$this->template->set_layout("client/themes/titan.php");
	}

	public function index() 
	{
		
	}

	public function add_post() 
	{
		$content = $this->common->nohtml($this->input->post("content"));
		$image_url = $this->common->nohtml($this->input->post("image_url"));
		$youtube_url = $this->common->nohtml($this->input->post("youtube_url"));

		$targetid = intval($this->input->post("targetid"));
		$target_type = $this->common->nohtml($this->input->post("target_type"));

		$with_users = ($this->input->post("with_users"));
		$post_as = $this->common->nohtml($this->input->post("post_as"));


		$c = $this->common->get_user_tag_usernames($content);
		$content = $c['content'];
		$tagged_users = $c['users'];

		$location = $this->common->nohtml($this->input->post("location"));

		$users = array();
		$user_flag = 0;
		if(is_array($with_users)) {
			foreach($with_users as $username) {
				$username = $this->common->nohtml($username);
				$user = $this->user_model->get_user_by_username($username);
				if($user->num_rows() > 0) {
					$user_flag = 1;
					$user = $user->row();
					$users[] = $user;
				}
			}
		}

		if($target_type == "page_profile") {
			// Validate page
			$page = $this->page_model->get_page($targetid);
			if($page->num_rows() == 0) {
				$this->template->jsonError(lang("error_94"));
			}

		}


		$fileid = 0;
		if(!empty($image_url)) {

			if($target_type == "page_profile") {
				// Check for default feed album
				$album = $this->image_model->get_page_feed_album($targetid);
				if($album->num_rows() == 0) {
					// Create
					$albumid = $this->image_model->add_album(array(
						"pageid" => $targetid,
						"feed_album" => 1,
						"name" => lang("ctn_646"),
						"description" => lang("ctn_647"),
						"timestamp" => time()
						)
					);
				} else {
					$album = $album->row();
					$albumid = $album->ID;
				}
			} else {
				// Check for default feed album
				$album = $this->image_model->get_user_feed_album($this->user->info->ID);
				if($album->num_rows() == 0) {
					// Create
					$albumid = $this->image_model->add_album(array(
						"userid" => $this->user->info->ID,
						"feed_album" => 1,
						"name" => lang("ctn_646"),
						"description" => lang("ctn_648"),
						"timestamp" => time()
						)
					);
				} else {
					$album = $album->row();
					$albumid = $album->ID;
				}
			}

			 $fileid = $this->feed_model->add_image(array(
            	"file_url" => $image_url,
            	"userid" => $this->user->info->ID,
            	"timestamp" => time(),
            	"albumid" => $albumid
            	)
            );
            // Update album count
            $this->image_model->increase_album_count($albumid);

		} elseif(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
			$this->load->library("upload");
			// Upload image
			$this->upload->initialize(array(
			   "upload_path" => $this->settings->info->upload_path,
		       "overwrite" => FALSE,
		       "max_filename" => 300,
		       "encrypt_name" => TRUE,
		       "remove_spaces" => TRUE,
		       "allowed_types" => "png|gif|jpeg|jpg",
		       "max_size" => $this->settings->info->file_size,
				)
			);

			if ( ! $this->upload->do_upload('image_file'))
            {
                    $error = array('error' => $this->upload->display_errors());

                    $this->template->jsonError(lang("error_95") . "<br /><br />" .
                    	 $this->upload->display_errors());
            }

            $data = $this->upload->data();

            if($target_type == "page_profile") {
				// Check for default feed album
				$album = $this->image_model->get_page_feed_album($targetid);
				if($album->num_rows() == 0) {
					// Create
					$albumid = $this->image_model->add_album(array(
						"pageid" => $targetid,
						"feed_album" => 1,
						"name" => lang("ctn_646"),
						"description" => lang("ctn_647"),
						"timestamp" => time()
						)
					);
				} else {
					$album = $album->row();
					$albumid = $album->ID;
				}
			} else {
				// Check for default feed album
				$album = $this->image_model->get_user_feed_album($this->user->info->ID);
				if($album->num_rows() == 0) {
					// Create
					$albumid = $this->image_model->add_album(array(
						"userid" => $this->user->info->ID,
						"feed_album" => 1,
						"name" => lang("ctn_646"),
						"description" => lang("ctn_648"),
						"timestamp" => time()
						)
					);
				} else {
					$album = $album->row();
					$albumid = $album->ID;
				}
			}


            $fileid = $this->feed_model->add_image(array(
            	"file_name" => $data['file_name'],
            	"file_type" => $data['file_type'],
            	"extension" => $data['file_ext'],
            	"file_size" => $data['file_size'],
            	"userid" => $this->user->info->ID,
            	"timestamp" => time(),
            	"albumid" => $albumid
            	)
            );
            // Update album count
            $this->image_model->increase_album_count($albumid);
		}

		// Video
		$videoid=0;
		if(!empty($youtube_url)) {
			$matches = array();
			preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
			if(!isset($matches[0]) || empty($matches[0])) {
				$this->template->jsonError(lang("error_96"));
			}
			$youtube_id = $matches[0];
			// Add
			$videoid = $this->feed_model->add_video(array(
				"youtube_id" => $youtube_id,
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );
		} elseif(isset($_FILES['video_file']['size']) && $_FILES['video_file']['size'] > 0) {
			$this->load->library("upload");
			// Upload image
			$this->upload->initialize(array(
			   "upload_path" => $this->settings->info->upload_path,
		       "overwrite" => FALSE,
		       "max_filename" => 300,
		       "encrypt_name" => TRUE,
		       "remove_spaces" => TRUE,
		       "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv",
		       "max_size" => $this->settings->info->file_size,
				)
			);

			if ( ! $this->upload->do_upload('video_file'))
            {
                    $error = array('error' => $this->upload->display_errors());

                    $this->template->jsonError(lang("error_97") . "<br /><br />" .
                    	 $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']));
            }

            $data = $this->upload->data();

            $videoid = $this->feed_model->add_video(array(
            	"file_name" => $data['file_name'],
            	"file_type" => $data['file_type'],
            	"extension" => $data['file_ext'],
            	"file_size" => $data['file_size'],
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );
		}


		if(empty($content) && $fileid == 0 && $videoid == 0) {
			$this->template->jsonError(lang("error_98"));
		}

		$site_flag = 0;
		$url_matches = array();
		preg_match_all('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/\-?:@=_#&%~,+$]+/', 
			$content, $url_matches);

		if(isset($url_matches[0])) {
			$url_matches = $url_matches[0];
		}

		// Hashtags
		$hashtags = $this->common->get_hashtags($content);
		
		foreach($hashtags[0] as $r) {
			$r = trim($r);
			$tag = substr($r, 1, strlen($r));
			// Check it exists
			$tagi = $this->feed_model->get_hashtag($tag);
			if($tagi->num_rows() == 0) {
				$this->feed_model->add_hashtag(array(
					"hashtag" => $tag,
					"count" => 1
					)
				);
			} else {
				$tagi = $tagi->row();
				$this->feed_model->increment_hashtag($tagi->ID);
			}
		}

		// Get urls in post
		$sites = array();
		foreach($url_matches as $k=>$v) {
			$s = $this->common->get_url_details($v);
			
			if(is_array($s)) {
				$sites[] = $s;
				$site_flag = 1;
				// Replace url in content
				$content = str_replace($v, "", $content);
			}
		}

		if($target_type == "user_profile") {
			// Validate user
			$user = $this->user_model->get_user_by_id($targetid);
			if($user->num_rows() == 0) {
				$this->template->jsonError(lang("error_85"));
			}
			$user = $user->row();

			// Check the user's permissions
			$flags = $this->common->check_friend($this->user->info->ID, $user->ID);
			if( ($user->post_profile && ($this->user->info->ID == $user->ID 
				|| $flags['friend_flag'])) || !$user->post_profile) {

			} else {
				$this->template->jsonError(lang("error_99"));
			}

			
			$postid = $this->feed_model->add_post(array(
				"userid" => $targetid,
				"content" => $content,
				"timestamp" => time(),
				"imageid" => $fileid,
				"videoid" => $videoid,
				"location" => $location,
				"user_flag" => $user_flag,
				"profile_userid" => $this->user->info->ID,
				"site_flag" => $site_flag
				)
			);
		} elseif($target_type == "page_profile") {
			// Validate page

			$page = $this->page_model->get_page($targetid);
			if($page->num_rows() == 0) {
				$this->template->jsonError(lang("error_94"));
			}

			$page = $page->row();

			// Get page member
			$member = $this->page_model->get_page_user($page->ID, $this->user->info->ID);
			if($member->num_rows() == 0) {
				$member = null;
			} else {
				$member = $member->row();
			}

			if(!$this->common->has_permissions(array("admin", "page_admin"), $this->user)) {
				if($post_as == "user") {
					// fine
					if($page->posting_status == 1 && $member == null) {
						$this->template->jsonError(lang("error_100"));
					} elseif($page->posting_status == 0 && ($member == null || !$member->roleid)) {
						$this->template->jsonError(lang("error_100"));
					}

					$this->user_model->increase_posts($this->user->info->ID);
				} elseif($post_as == "page") {
					// check they are admin of page
					if(!isset($member->roleid)) {
						$this->template->jsonError(lang("error_100"));
					} elseif($member->roleid != 1) {
						$this->template->jsonError(lang("error_100"));
					}
					
				} else {
					$this->template->jsonError(lang("error_100"));
				}
			}
			
			$postid = $this->feed_model->add_post(array(
				"userid" => $this->user->info->ID,
				"pageid" => $targetid,
				"content" => $content,
				"timestamp" => time(),
				"imageid" => $fileid,
				"videoid" => $videoid,
				"location" => $location,
				"user_flag" => $user_flag,
				"hide_profile" => 1, // stops it showing up in feed and profile page,
				"post_as" => $post_as,
				"site_flag" => $site_flag
				)
			);
		} else {
			$this->user_model->increase_posts($this->user->info->ID);
			$postid = $this->feed_model->add_post(array(
				"userid" => $this->user->info->ID,
				"content" => $content,
				"timestamp" => time(),
				"imageid" => $fileid,
				"videoid" => $videoid,
				"location" => $location,
				"user_flag" => $user_flag,
				"site_flag" => $site_flag
				)
			);
		}

		$this->feed_model->add_feed_subscriber(array(
			"postid" => $postid,
			"userid" => $this->user->info->ID
			)
		);

		foreach($sites as $site) 
		{
			$this->feed_model->add_feed_site(array(
				"url" => $site['url'],
				"title" => $site['title'],
				"description" => $site['description'],
				"image" => $site['image'],
				"postid" => $postid
				)
			);
		}

		foreach($tagged_users as $user) {
			// Notification
			$this->feed_model->add_tagged_users(array(
				"userid" => $user->ID,
				"postid" => $postid
				)
			);
			$this->user_model->increment_field($user->ID, "noti_count", 1);
			$this->user_model->add_notification(array(
				"userid" => $user->ID,
				"url" => "home/index/3?postid=" . $postid,
				"timestamp" => time(),
				"message" => $this->user->info->first_name . " " . $this->user->info->last_name . lang("ctn_649"),
				"status" => 0,
				"fromid" => $this->user->info->ID,
				"username" => $user->username,
				"email" => $user->email,
				"email_notification" => $user->email_notification
				)
			);

			$this->feed_model->add_feed_subscriber(array(
				"postid" => $postid,
				"userid" => $user->ID
				)
			);
		}

		foreach($users as $user) {
			$this->feed_model->add_feed_users(array(
				"userid" => $user->ID,
				"postid" => $postid
				)
			);

			// Check user is not already added to subscriber feed
			$sub = $this->feed_model->get_feed_subscriber($postid, $user->ID);
			if($sub->num_rows() == 0) {
				$this->feed_model->add_feed_subscriber(array(
					"postid" => $postid,
					"userid" => $user->ID
					)
				);
			}

			// Notification
			$this->user_model->increment_field($user->ID, "noti_count", 1);
			$this->user_model->add_notification(array(
				"userid" => $user->ID,
				"url" => "home/index/3?postid=" . $postid,
				"timestamp" => time(),
				"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_650"),
				"status" => 0,
				"fromid" => $this->user->info->ID,
				"username" => $user->username,
				"email" => $user->email,
				"email_notification" => $user->email_notification
				)
			);
		}

		//$this->session->set_flashdata("globalmsg", "Post posted!");
		//redirect(site_url());

		echo json_encode(array(
			"success" => 1
			)
		);
		exit();
	}

	public function load_home_posts() 
	{
		$page = intval($this->input->get("page"));
		$posts = $this->feed_model->get_home_feed($this->user, $page);

		$page = $page + 10;
		$url = site_url("feed/load_home_posts?page=" . $page);

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}

	public function load_all_posts() 
	{
		if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user))
		{
			$this->template->jsonError(lang("error_101"));
		}
		$page = intval($this->input->get("page"));
		$posts = $this->feed_model->get_all_feed($this->user->info->ID, $page);

		$page = $page + 10;
		$url = site_url("feed/load_all_posts?page=" . $page);

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}

	public function load_single_post($postid) 
	{
		$postid = intval($postid);
		$posts = $this->feed_model->get_post($postid, $this->user->info->ID);

		

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			),1
		);
	}

	public function load_hashtag_posts() 
	{
		$page = intval($this->input->get("page"));
		$hashtag = $this->common->nohtml($this->input->get("hashtag"));
		$posts = $this->feed_model->get_hashtag_feed($hashtag, $this->user->info->ID, $page);


		$page = $page + 10;
		$url = site_url("feed/load_hashtag_posts?hashtag=" . $hashtag . "&page=" . $page);

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}

	public function load_saved_posts() 
	{
		$page = intval($this->input->get("page"));
		$posts = $this->feed_model->get_saved_feed($this->user->info->ID, $page);

		$page = $page + 10;
		$url = site_url("feed/load_saved_posts?page=" . $page);

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}

	public function load_user_posts($userid) 
	{
		$userid = intval($userid);
		$page = intval($this->input->get("page"));

		$user = $this->user_model->get_user_by_id($userid);
		if($user->num_rows() == 0) $this->template->errori(lang("error_52"));
		$user = $user->row();

		$flags = $this->common->check_friend($this->user->info->ID, $user->ID);

		if($user->posts_view == 1 && $user->ID != $this->user->info->ID) {
			// Only let's friends view profile.
			if(!$flags['friend_flag']) {

				exit();
			}
		}


		$posts = $this->feed_model->get_user_posts_only($userid, $page);

		$page = $page + 10;
		$url = site_url("feed/load_user_posts/" . $userid . "?page=" . $page);

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}



	public function load_page_posts($pageid) 
	{
		$pageid = intval($pageid);
		$page = intval($this->input->get("page"));

		$pageR = $this->page_model->get_page($pageid);
		if($pageR->num_rows() == 0) {
			$this->template->errori(lang("error_94"));
		}
		$pageR = $pageR->row();

		$posts = $this->feed_model->get_page_posts($pageid, $this->user->info->ID, $page);

		$page = $page + 10;
		$url = site_url("feed/load_page_posts/" . $pageid . "?page=" . $page);

		// Get page member
		$member = $this->page_model->get_page_user($pageid, $this->user->info->ID);
		if($member->num_rows() == 0) {
			$member = null;
		} else {
			$member = $member->row();
		}

		if($pageR->type == 1) {
			// Check user is a member
			if($member == null) {
	
				// Check for page invite
				$invite = $this->page_model->get_page_invite($pageR->ID, $this->user->info->ID);
				if($invite->num_rows() ==0) {
					if(!$this->common->has_permissions(array("admin", "page_admin"), $this->user)) {
						$this->template->error(lang("error_102"));
					}
				}
			}
		}

		$this->template->loadAjax("feed/feed.php", array(
			"posts" => $posts,
			"a_url" => $url
			),1
		);
	}

	private function check_post_permission($post) 
	{
		if($post->pageid > 0) {
			// Check page posting status
			$page = $this->page_model->get_page($post->pageid);
			if($page->num_rows() == 0) {
				$this->template->jsonError(lang("error_94"));
			}
			$page = $page->row();
			if(!$this->common->has_permissions(array("admin", "page_admin"), $this->user)) {

				if($page->type) {
					// Private, so only allow members to like
					// Get page member
					$member = $this->page_model->get_page_user($post->pageid, $this->user->info->ID);
					if($member->num_rows() == 0) {
						$member = null;
					} else {
						$member = $member->row();
					}
					if($member == null) {
						$this->template
							->jsonError(lang("error_103"));
					}
				}
			}

		} elseif($post->userid > 0) {
			// check user's permission
			if($post->posts_view == 1) {
				// Only friends can like/comment
				$flags = $this->common->check_friend($this->user->info->ID, $post->userid);
				if(!$flags['friend_flag']) {
					$this->template->jsonError(lang("error_104"));
				}
			}
		}
	}

	public function like_post($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		// Check user hasn't already liked the post
		$like = $this->feed_model->get_post_like($id, $this->user->info->ID);
		if($like->num_rows() > 0) {
			// Unlike
			$like  = $like->row();
			$likes = $post->likes - 1;
			$this->feed_model->update_post($id, array(
				"likes" => $likes
				)
			);

			$this->feed_model->delete_like_post($like->ID);

			$data = array(
				"likes" => $likes,
				"like_status" => false
			);
		} else {
			$likes = $post->likes + 1;
			$this->feed_model->update_post($id, array(
				"likes" => $likes
				)
			);

			$this->feed_model->add_post_like(array(
				"userid" => $this->user->info->ID,
				"postid" => $id,
				"timestamp" => time()
				)
			);

			$data = array(
				"likes" => $likes,
				"like_status" => true
			);

			if($post->userid > 0) {
				$this->user_model->increment_field($post->userid, "noti_count", 1);
				$this->user_model->add_notification(array(
					"userid" => $post->userid,
					"url" => "home/index/3?postid=" . $post->ID,
					"timestamp" => time(),
					"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_651"),
					"status" => 0,
					"fromid" => $this->user->info->ID,
					"username" => $post->username,
					"email" => $post->email,
					"email_notification" => $post->email_notification
					)
				);
			}

		}

		echo json_encode($data);
		exit();
	}

	public function get_single_comment($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$commentid = intval($this->input->get("commentid"));

		$page = 0;

		$comments = $this->feed_model->get_single_comment($id, $this->user->info->ID, $commentid);
		$com = array();
		foreach($comments->result() as $r) {
			$com[] = $r;
		}

		$com = array_reverse($com);

		$this->template->loadAjax("feed/feed_comments.php", array(
			"com" => $com,
			"post" => $post,
			"page" => $page,
			"hide_prev" => 0
			),1
		);
	}

	public function get_feed_comments($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$page = 0;

		$comments = $this->feed_model->get_feed_comments($id, $this->user->info->ID, $page);
		$com = array();
		foreach($comments->result() as $r) {
			$com[] = $r;
		}

		$com = array_reverse($com);

		$this->template->loadAjax("feed/feed_comments.php", array(
			"com" => $com,
			"post" => $post,
			"page" => $page,
			"hide_prev" => 0
			),1
		);
	}

	public function get_previous_comments($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$page = intval($this->input->get("page"));

		$comments = $this->feed_model->get_feed_comments($id, $this->user->info->ID, $page);
		$com = array();
		foreach($comments->result() as $r) {
			$com[] = $r;
		}

		$com = array_reverse($com);

		$comments_left = $post->comments - $page;
		$post->comments = $comments_left;

		$this->template->loadAjax("feed/feed_comments_single.php", array(
			"com" => $com,
			"post" => $post,
			"page" => $page,
			"hide_prev" => 0
			),1
		);
	}

	public function post_comment($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$comment = $this->common->nohtml($this->input->post("comment"));

		if(empty($comment)) $this->template->jsonError(lang("error_106"));

		$c = $this->common->get_user_tag_usernames($comment);
		$comment = $c['content'];
		$tagged_users = $c['users'];


		$hide_prev = intval($this->input->get("hide_prev"));

		$page = intval($this->input->post("page"));

		$commentid = $this->feed_model->add_comment(array(
			"postid" => $post->ID,
			"userid" => $this->user->info->ID,
			"comment" => $comment,
			"timestamp" => time()
			)
		);

		$comments_count = $post->comments+1;
		$this->feed_model->update_post($id, array(
			"comments" => $comments_count
			)
		);

		foreach($tagged_users as $user) {
		
			$this->user_model->increment_field($user->ID, "noti_count", 1);
			$this->user_model->add_notification(array(
				"userid" => $user->ID,
				"url" => "home/index/3?postid=" . $post->ID . "&commentid=". $commentid,
				"timestamp" => time(),
				"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_652"),
				"status" => 0,
				"fromid" => $this->user->info->ID,
				"username" => $user->username,
				"email" => $user->email,
				"email_notification" => $user->email_notification
				)
			);
		}

		// Check user is not already added to subscriber feed
		$sub = $this->feed_model->get_feed_subscriber($post->ID, $this->user->info->ID);
		if($sub->num_rows() == 0) {
			$this->feed_model->add_feed_subscriber(array(
				"postid" => $post->ID,
				"userid" => $this->user->info->ID
				)
			);
		}

		// get subscribers
		$subs = $this->feed_model->get_feed_subscribers($id);
		foreach($subs->result() as $user) {
			if($user->ID != $this->user->info->ID) {
				$this->user_model->increment_field($user->ID, "noti_count", 1);
				$this->user_model->add_notification(array(
					"userid" => $user->ID,
					"url" => "home/index/3?postid=" . $id . "&commentid=" . $commentid,
					"timestamp" => time(),
					"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_653"),
					"status" => 0,
					"fromid" => $this->user->info->ID,
					"username" => $user->username,
					"email" => $user->email,
					"email_notification" => $user->email_notification
					)
				);
			}
		}

		$comments = $this->feed_model->get_feed_comments($id, $this->user->info->ID, $page);
		$com = array();
		foreach($comments->result() as $r) {
			$com[] = $r;
		}

		$com = array_reverse($com);



		$ajax = $this->template->returnAjax("feed/feed_comments_single.php", array(
			"com" => $com,
			"post" => $post,
			"page" => $page,
			"hide_prev" => $hide_prev
			)
		);

		echo json_encode(array(
			"content" => $ajax,
			"comments" => $comments_count
			)
		);
		exit();
	}

	public function get_post_likes($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$likes = $this->feed_model->get_post_likes($id);
		$this->template->loadAjax("feed/feed_likes.php", array(
			"likes" => $likes
			),1
		);
	}

	public function like_comment($id) 
	{
		$hash  = $this->input->get("hash");
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}

		$id = intval($id);
		$comment = $this->feed_model->get_comment($id);
		if($comment->num_rows() == 0) {
			$this->template->jsonError(lang("error_107"));
		}

		$comment = $comment->row();

		if($comment->postid > 0) {
			$post = $this->feed_model->get_post($comment->postid,$this->user->info->ID);
			if($post->num_rows() == 0) {
				$this->template->jsonError(lang("error_105"));
			}
			$post = $post->row();

			$this->check_post_permission($post);
		}

		// Check for like
		$like = $this->feed_model->get_comment_like($id, $this->user->info->ID);
		if($like->num_rows() > 0) {
			// Unlike
			$like = $like->row();

			$likes = $comment->likes - 1;

			$this->feed_model->update_comment($id, array(
				"likes" => $comment->likes - 1,
				)
			);

			$like_status = 0;

			// Delete
			$this->feed_model->delete_comment_like($like->ID);
		} else {

			$likes = $comment->likes+1;
			$this->feed_model->update_comment($id, array(
				"likes" => $comment->likes + 1,
				)
			);

			$like_status = 1;

			$this->feed_model->add_comment_like(array(
				"userid" => $this->user->info->ID,
				"commentid" => $id,
				"timestamp" => time()
				)
			);
		}

		echo json_encode(array(
			"likes" => $likes,
			"like_status" => $like_status
			)
		);

	}

	public function get_feed_comments_replies($id) 
	{
		$id = intval($id);
		$comment = $this->feed_model->get_comment($id);
		if($comment->num_rows() == 0) {
			$this->template->error(lang("error_108"));
		}
		$comment = $comment->row();

		$post = $this->feed_model->get_post($comment->postid,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$replies = $this->feed_model->get_comment_replies($id, $this->user->info->ID, 0);

		$com = array();
		foreach($replies->result() as $r) {
			$com[] = $r;
		}

		$com = array_reverse($com);

		$this->template->loadAjax("feed/feed_comment_replies.php", array(
			"comment" => $comment,
			"com" => $com
			),1
		);
	}

	public function post_comment_reply($id) 
	{
		$id = intval($id);
		$com = $this->feed_model->get_comment($id);
		if($com->num_rows() == 0) {
			$this->template->jsonError(lang("error_107") . $id);
		}
		$com = $com->row();

		$post = $this->feed_model->get_post($com->postid,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		$comment = $this->common->nohtml($this->input->post("comment"));

		if(empty($comment)) $this->template->jsonError(lang("error_106"));

		$hide_prev = intval($this->input->get("hide_prev"));

		$page = intval($this->input->post("page"));

		$replyid = $this->feed_model->add_comment(array(
			"commentid" => $com->ID,
			"userid" => $this->user->info->ID,
			"comment" => $comment,
			"timestamp" => time()
			)
		);

		$reply_count = $com->replies+1;
		$this->feed_model->update_comment($id, array(
			"replies" => $reply_count
			)
		);

		$this->feed_model->increment_post($com->postid);
		$comments = $com->comments+1;

		if($com->userid>0) {
			$this->user_model->increment_field($com->userid, "noti_count", 1);
			$this->user_model->add_notification(array(
				"userid" => $com->userid,
				"url" => "home/index/3?postid=" . $post->ID . "&commentid=" . $com->ID . "&replyid=" . $replyid,
				"timestamp" => time(),
				"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_654"),
				"status" => 0,
				"fromid" => $this->user->info->ID,
				"username" => $com->username,
				"email" => $com->email,
				"email_notification" => $com->email_notification
				)
			);
		}

		$replies = $this->feed_model->get_comment_replies($id, $this->user->info->ID, 0);
		$coms = array();
		foreach($replies->result() as $r) {
			$coms[] = $r;
		}

		$coms = array_reverse($coms);


		$ajax = $this->template->returnAjax("feed/feed_comment_replies_single.php", array(
			"com" => $coms,
			"comment" => $com,
			)
		);

		echo json_encode(array(
			"content" => $ajax,
			"comments" => $reply_count,
			"comments_count" => $comments,
			"feeditemid" => $com->feeditemid
			)
		);
		exit();
	}

	public function edit_post($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		if($post->pageid > 0 && $post->post_as == "page") {
			// Anyone who is admin of page can modify the post
			$member = $this->page_model->get_page_user($post->pageid, $this->user->info->ID);
			if($member->num_rows() == 0) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			} else {
				$member = $member->row();
				if($member->roleid != 1) {
					if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
						$this->template->errori(lang("error_109"));
					}
				}
			}

		} else {
			if($post->userid != $this->user->info->ID) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			}
		}

		$users = $this->feed_model->get_feed_users($id);

		$this->template->loadAjax("feed/edit_post.php", array(
			"post" => $post,
			"users" => $users
			)
		);
	}

	public function edit_post_pro($id) 
	{
		$id = intval($id);
		$post = $this->feed_model->get_post($id, $this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		if($post->pageid > 0 && $post->post_as == "page") {
			// Anyone who is admin of page can modify the post
			$member = $this->page_model->get_page_user($post->pageid, $this->user->info->ID);
			if($member->num_rows() == 0) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			} else {
				$member = $member->row();
				if($member->roleid != 1) {
					if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
						$this->template->errori(lang("error_109"));
					}
				}
			}

		} else {
			if($post->userid != $this->user->info->ID) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			}
		}

		$content = $this->common->nohtml($this->input->post("content"));
		$location = $this->common->nohtml($this->input->post("location"));
		$image_url = $this->common->nohtml($this->input->post("image_url"));
		$youtube_url = $this->common->nohtml($this->input->post("youtube_url"));
		$with_users = ($this->input->post("with_users"));

		$c = $this->common->get_user_tag_usernames($content);
		$content = $c['content'];
		$tagged_users = $c['users'];

		$users = array();
		$user_flag = 0;
		if(is_array($with_users)) {
			foreach($with_users as $username) {
				$username = $this->common->nohtml($username);
				$user = $this->user_model->get_user_by_username($username);
				if($user->num_rows() > 0) {
					$user_flag = 1;
					$user = $user->row();
					$users[] = $user;
				}
			}
		}

		$fileid = $post->imageid;
		if(!empty($image_url)) {
			 $fileid = $this->feed_model->add_image(array(
            	"file_url" => $image_url,
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );

		} elseif(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
			$this->load->library("upload");
			// Upload image
			$this->upload->initialize(array(
			   "upload_path" => $this->settings->info->upload_path,
		       "overwrite" => FALSE,
		       "max_filename" => 300,
		       "encrypt_name" => TRUE,
		       "remove_spaces" => TRUE,
		       "allowed_types" => "png|gif|jpeg|jpg",
		       "max_size" => $this->settings->info->file_size,
				)
			);

			if ( ! $this->upload->do_upload('image_file'))
            {
                    $error = array('error' => $this->upload->display_errors());

                    $this->template->jsonError(lang("error_95") . "<br /><br />" .
                    	 $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $fileid = $this->feed_model->add_image(array(
            	"file_name" => $data['file_name'],
            	"file_type" => $data['file_type'],
            	"extension" => $data['file_ext'],
            	"file_size" => $data['file_size'],
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );
		}

		// Video
		$videoid=0;
		if(!empty($youtube_url)) {
			$matches = array();
			preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
			if(!isset($matches[0]) || empty($matches[0])) {
				$this->template->error(lang("error_96"));
			}
			$youtube_id = $matches[0];
			// Add
			$videoid = $this->feed_model->add_video(array(
				"youtube_id" => $youtube_id,
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );
		} elseif(isset($_FILES['video_file']['size']) && $_FILES['video_file']['size'] > 0) {
			$this->load->library("upload");
			// Upload image
			$this->upload->initialize(array(
			   "upload_path" => $this->settings->info->upload_path,
		       "overwrite" => FALSE,
		       "max_filename" => 300,
		       "encrypt_name" => TRUE,
		       "remove_spaces" => TRUE,
		       "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv",
		       "max_size" => $this->settings->info->file_size,
				)
			);

			if ( ! $this->upload->do_upload('video_file'))
            {
                    $error = array('error' => $this->upload->display_errors());

                    $this->template->error(lang("error_97") . "<br /><br />" .
                    	 $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']));
            }

            $data = $this->upload->data();

            $videoid = $this->feed_model->add_video(array(
            	"file_name" => $data['file_name'],
            	"file_type" => $data['file_type'],
            	"extension" => $data['file_ext'],
            	"file_size" => $data['file_size'],
            	"userid" => $this->user->info->ID,
            	"timestamp" => time()
            	)
            );
		}

		if(empty($content) && $fileid == 0 && $videoid == 0) $this->template->jsonError(lang("error_98"));

		$this->feed_model->update_post($id, array(
			"content" => $content,
			"location" => $location,
			"imageid" => $fileid,
			"videoid" => $videoid,
			"user_flag" => $user_flag
			)
		);

		foreach($tagged_users as $user) {
			// Check the user wasn't already tagged
			$tag = $this->feed_model->get_feed_tag($id, $user->ID);
			if($tag->num_rows() == 0) {

				// Notification
				$this->feed_model->add_tagged_users(array(
					"userid" => $user->ID,
					"postid" => $id
					)
				);
				$this->user_model->increment_field($user->ID, "noti_count", 1);
				$this->user_model->add_notification(array(
					"userid" => $user->ID,
					"url" => "home/index/3?postid=" . $id,
					"timestamp" => time(),
					"message" => $this->user->info->first_name . " " . $this->user->info->last_name . " " . lang("ctn_649"),
					"status" => 0,
					"fromid" => $this->user->info->ID,
					"username" => $user->username,
					"email" => $user->email,
					"email_notification" => $user->email_notification
					)
				);

				// Check user is not already added to subscriber feed
				$sub = $this->feed_model->get_feed_subscriber($id, $user->ID);
				if($sub->num_rows() == 0) {
					$this->feed_model->add_feed_subscriber(array(
						"postid" => $id,
						"userid" => $user->ID
						)
					);
				}

			}

		}

		// Delete feed users
		$this->feed_model->delete_feed_users($id);

		$users = array_unique($users);

		foreach($users as $user) {
			$this->feed_model->add_feed_users(array(
				"userid" => $user->ID,
				"postid" => $id
				)
			);

			// Check user is not already added to subscriber feed
			$sub = $this->feed_model->get_feed_subscriber($id, $user->ID);
			if($sub->num_rows() == 0) {
				$this->feed_model->add_feed_subscriber(array(
					"postid" => $id,
					"userid" => $user->ID
					)
				);
			}

		}

		// Get the post for display
		$post = $this->feed_model->get_post($id, $this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$ajax = $this->template->returnAjax("feed/feed_single.php", array(
			"r" => $post
			)
		);


		echo json_encode(array(
			"success" => 1,
			"post" => $ajax,
			"id" => $id
			)
		);
		exit();

	}

	public function delete_post($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		if($post->pageid > 0 && $post->post_as == "page") {
			// Anyone who is admin of page can modify the post
			$member = $this->page_model->get_page_user($post->pageid, $this->user->info->ID);
			if($member->num_rows() == 0) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			} else {
				$member = $member->row();
				if($member->roleid != 1) {
					if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
						$this->template->errori(lang("error_109"));
					}
				}
			}

		} else {
			if($post->userid != $this->user->info->ID) {
				if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
					$this->template->errori(lang("error_109"));
				}
			}

			$this->user_model->decrease_posts($post->userid);
		}
		$this->feed_model->delete_post($id);

		echo json_encode(array(
			"success" => 1
			)
		);
		exit();
	}

	public function save_post($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$this->check_post_permission($post);

		// Check user has saved post
		$saved = $this->feed_model->get_user_save_post($id, $this->user->info->ID);
		if($saved->num_rows() == 0) {
			// Add
			$this->feed_model->add_saved_post(array(
				"userid" => $this->user->info->ID,
				"postid" => $id
				)
			);
			$status = 1;
		} else {
			$saved = $saved->row();
			$this->feed_model->delete_saved_post($saved->ID);
			$status = 0;
		}

		echo json_encode(array(
			"success" => 1,
			"status" => $status
			)
		);
		exit();
	}

	public function subscribe_post($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}
		$id = intval($id);
		$post = $this->feed_model->get_post($id,$this->user->info->ID);
		if($post->num_rows() == 0) {
			$this->template->jsonError(lang("error_105"));
		}
		$post = $post->row();

		$sub = $this->feed_model->get_feed_subscriber($id, $this->user->info->ID);
		if($sub->num_rows() == 0) {
			$this->check_post_permission($post);

			$this->feed_model->add_feed_subscriber(array(
				"postid" => $post->ID,
				"userid" => $this->user->info->ID
				)
			);
			$status = 1;
		} else {
			$sub = $sub->row();

			$this->feed_model->delete_feed_subscribe($sub->ID);
			$status = 0;
		}

		echo json_encode(array(
			"success" => 1,
			"status" => $status
			)
		);
		exit();
	}

	public function delete_feed_comment($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}
		$id = intval($id);
		$comment = $this->feed_model->get_comment($id);
		if($comment->num_rows() == 0) {
			$this->template->jsonError(lang("error_107"));
		}

		$comment = $comment->row();

		if($comment->userid != $this->user->info->ID && (!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) ) {
			$this->template->jsonError(lang("error_110"));
		}

		$this->feed_model->delete_comment($id);

		if($comment->postid > 0) {
			$comments_count = $comment->comments-1;
			$this->feed_model->update_post($comment->postid, array(
				"comments" => $comments_count
				)
			);
		}

		echo json_encode(array(
			"success" => 1,
			)
		);
		exit();
	}

	public function delete_feed_comment_reply($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->jsonError(lang("error_6"));
		}
		$id = intval($id);
		$comment = $this->feed_model->get_comment($id);
		if($comment->num_rows() == 0) {
			$this->template->jsonError(lang("error_107"));
		}

		$comment = $comment->row();

		if($comment->userid != $this->user->info->ID && (!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) ) {
			$this->template->jsonError(lang("error_110"));
		}

		$this->feed_model->delete_comment($id);

		if(isset($comment->fcpostid) && $comment->fcpostid > 0) {
			$comments_count = $comment->fc_item_comments-1;
			$this->feed_model->update_post($comment->fcpostid, array(
				"comments" => $comments_count
				)
			);
		}

		echo json_encode(array(
			"success" => 1,
			)
		);
		exit();
	}

}

?>