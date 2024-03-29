<?php

class Feed_Model extends CI_Model 
{

	public function add_post($data) 
	{
		$this->db->insert("feed_item", $data);
		return $this->db->insert_ID();
	}

	public function get_home_feed_search($user,$search) 
	{
		/*
		$friends = unserialize($user->info->friends);
		$this->db->group_start();
		if(is_array($friends)) {
			foreach($friends as $friend) {
				$this->db->or_group_start();
				$this->db->or_where("feed_item.userid", $friend);
				$this->db->where("feed_item.post_as !=", "page");
				$this->db->group_end();
			}
		}
		$this->db->or_where("feed_item.userid", $user->info->ID);
		
		$pages = unserialize($user->info->pages);
		if(is_array($pages)) {
			foreach($pages as $pageid) {
				$this->db->or_where("feed_item.pageid", $pageid);
			}
		}
		
		$this->db->group_end();
		*/
		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as, feed_item.type,feed_item.categoryid,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $user, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $user, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $user, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $user, "LEFT OUTER")
			->like('content', $search)
			->order_by("feed_item.ID", "DESC")
			->get("feed_item");
	}

	public function get_home_feed($user, $page) 
	{
		$friends = unserialize($user->info->friends);
		$this->db->group_start();
		if(is_array($friends)) {
			foreach($friends as $friend) {
				$this->db->or_group_start();
				$this->db->or_where("feed_item.userid", $friend);
				$this->db->where("feed_item.post_as !=", "page");
				$this->db->group_end();
			}
		}
		$this->db->or_where("feed_item.userid", $user->info->ID);

		$pages = unserialize($user->info->pages);
		if(is_array($pages)) {
			foreach($pages as $pageid) {
				$this->db->or_where("feed_item.pageid", $pageid);
			}
		}
		$this->db->group_end();

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $user->info->ID, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $user->info->ID, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $user->info->ID, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $user->info->ID, "LEFT OUTER")
			->limit(10, $page)
			->order_by("feed_item.ID", "DESC")
			->get("feed_item");
	}

	public function get_home_feed_api($user, $page) 
	{
		$friends = unserialize($user->friends);
		$this->db->group_start();
		if(is_array($friends)) {
			foreach($friends as $friend) {
				$this->db->or_group_start();
				$this->db->or_where("feed_item.userid", $friend);
				$this->db->where("feed_item.post_as !=", "page");
				$this->db->group_end();
			}
		}
		$this->db->or_where("feed_item.userid", $user->ID);

		$pages = unserialize($user->pages);
		if(is_array($pages)) {
			foreach($pages as $pageid) {
				$this->db->or_where("feed_item.pageid", $pageid);
			}
		}
		$this->db->group_end();

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.shares,feed_item.share_pageid,feed_item.share_comment,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $user->ID, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $user->ID, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $user->ID, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $user->ID, "LEFT OUTER")
			->limit(10, $page)
			->order_by("feed_item.ID", "DESC")
			->get("feed_item");
	}

	public function get_all_feed($userid, $page) 
	{

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $userid, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->order_by("feed_item.ID", "DESC")
			->limit(10,$page)
			->get("feed_item");
	}

	public function get_hashtag_feed($hashtag, $userid, $page) 
	{
		$hashtag = "#" . $hashtag;
		$this->db->like("feed_item.content", $hashtag);

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $userid, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->order_by("feed_item.ID", "DESC")
			->limit(10,$page)
			->get("feed_item");
	}

	public function get_saved_feed($userid, $page) 
	{
		$this->db->where("user_saved_posts.userid", $userid);

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("feed_item", "feed_item.ID = user_saved_posts.postid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->order_by("feed_item.ID", "DESC")
			->limit(10, $page)
			->get("user_saved_posts");
	}

	public function get_user_posts_only($userid, $page) 
	{
		$this->db->where("feed_item.userid", $userid);
		$this->db->where("feed_item.hide_profile", 0);

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $userid, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->order_by("feed_item.ID", "DESC")
			->limit(10, $page)
			->get("feed_item");
	}

	public function get_page_posts($pageid, $userid, $page) 
	{
		$this->db->where("feed_item.pageid", $pageid);

		return $this->db
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $userid, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->order_by("feed_item.ID", "DESC")
			->limit(10, $page)
			->get("feed_item");
	}

	public function get_user_saved_post($id) {
		return $this->db->query("SELECT f.content, pc.`name` as category_name,u.first_name,u.last_name,f.`timestamp`,f.type,f.ID
		FROM user_saved_posts up LEFT JOIN feed_item f ON f.ID=up.postid 
		LEFT JOIN page_categories pc ON pc.ID=f.categoryid
		LEFT JOIN users u ON u.ID=f.userid
		WHERE up.userid = $id");
	}

	public function get_post($id, $userid) 
	{
		return $this->db
			->where("feed_item.ID", $id)
			->select("feed_item.ID, feed_item.content, feed_item.post_as,
				feed_item.timestamp, feed_item.userid, feed_item.likes,
				feed_item.comments, feed_item.location, feed_item.user_flag,
				feed_item.profile_userid, feed_item.template, feed_item.site_flag,
				user_images.ID as imageid, user_images.file_name as image_file_name,
				user_images.file_url as image_file_url,
				user_videos.ID as videoid, user_videos.file_name as video_file_name,
				user_videos.youtube_id, user_videos.extension as video_extension,
				users.username, users.first_name, users.last_name, users.avatar,
				users.posts_view, users.email, users.email_notification,
				feed_likes.ID as likeid,
				profile.username as p_username, profile.first_name as p_first_name,
				profile.last_name as p_last_name, profile.avatar as p_avatar,
				profile.online_timestamp as p_online_timestamp,
				user_albums.ID as albumid, user_albums.name as album_name,
				pages.ID as pageid, pages.name as page_name, 
				pages.profile_avatar as page_avatar, pages.slug as page_slug,
				calendar_events.title as event_title, calendar_events.description as event_description,
				calendar_events.start as event_start, calendar_events.end as event_end,
				calendar_events.location as event_location, calendar_events.ID as eventid,
				page_users.roleid,
				user_saved_posts.ID as savepostid,
				feed_item_subscribers.ID as subid")
			->join("users", "users.ID = feed_item.userid")
			->join("user_images", "user_images.ID = feed_item.imageid", "left outer")
			->join("user_videos", "user_videos.ID = feed_item.videoid", "left outer")
			->join("user_albums", "user_albums.ID = user_images.albumid", "left outer")
			->join("users as profile", "profile.ID = feed_item.profile_userid", "left outer")
			->join("pages", "pages.ID = feed_item.pageid", "left outer")
			->join("page_users", "page_users.pageid = feed_item.pageid AND page_users.userid = " . $userid, "LEFT OUTER")
			->join("calendar_events", "calendar_events.ID = feed_item.eventid", "left outer")
			->join("feed_likes", "feed_likes.postid = feed_item.ID AND feed_likes.userid = " . $userid, "LEFT OUTER")
			->join("user_saved_posts", "user_saved_posts.postid = feed_item.ID AND user_saved_posts.userid = " . $userid, "left outer")
			->join("feed_item_subscribers", "feed_item_subscribers.postid = feed_item.ID and feed_item_subscribers.userid = " . $userid, "LEFT OUTER")
			->get("feed_item");
	}

	public function update_post($id, $data) 
	{
		$this->db->where("ID", $id)->update("feed_item", $data);
	}

	public function delete_post($id) 
	{
		$this->db->where("ID", $id)->delete("feed_item");
	}

	public function get_post_like($id, $userid) 
	{
		return $this->db
			->where("feed_likes.postid", $id)
			->where("feed_likes.userid", $userid)
			->get("feed_likes");
	}

	public function delete_like_post($id) 
	{
		$this->db->where("ID", $id)->delete("feed_likes");
	}

	public function add_post_like($data) 
	{
		$this->db->insert("feed_likes", $data);
	}

	public function add_image($data) 
	{
		$this->db->insert("user_images", $data);
		return $this->db->insert_ID();
	}

	public function add_video($data) 
	{
		$this->db->insert("user_videos", $data);
		return $this->db->insert_ID();
	}

	public function get_post_likes($id) 
	{
		return $this->db->where("feed_likes.postid", $id)
			->select("users.username, users.first_name, users.last_name,
				users.online_timestamp, users.avatar")
			->join("users", "users.ID = feed_likes.userid")
			->get("feed_likes");
	}

	public function get_single_comment($id, $userid, $commentid) 
	{
		return $this->db
			->where("feed_item_comments.ID", $commentid)
			->where("feed_item_comments.postid", $id)
			->select("feed_item_comments.ID, feed_item_comments.timestamp, feed_item_comments.comment,
				feed_item_comments.userid, feed_item_comments.likes, feed_item_comments.replies,
				users.username, users.first_name, users.last_name, users.online_timestamp,
				users.avatar,
				feed_item_comment_likes.ID as commentlikeid")
			->join("users", "users.ID = feed_item_comments.userid")
			->join("feed_item_comment_likes", "feed_item_comment_likes.commentid = feed_item_comments.ID AND feed_item_comment_likes.userid = " . $userid, "LEFT OUTER")
			->get("feed_item_comments");
	}
	
	public function get_feed_comments($id, $userid, $page) 
	{
		return $this->db
			->where("feed_item_comments.postid", $id)
			->select("feed_item_comments.ID, feed_item_comments.timestamp, feed_item_comments.comment,
				feed_item_comments.userid, feed_item_comments.likes, feed_item_comments.replies,
				users.username, users.first_name, users.last_name, users.online_timestamp,
				users.avatar,
				feed_item_comment_likes.ID as commentlikeid")
			->join("users", "users.ID = feed_item_comments.userid")
			->join("feed_item_comment_likes", "feed_item_comment_likes.commentid = feed_item_comments.ID AND feed_item_comment_likes.userid = " . $userid, "LEFT OUTER")
			->limit(5, $page)
			->order_by("ID", "DESC")
			->get("feed_item_comments");
	}

	public function add_comment($data) 
	{
		$this->db->insert("feed_item_comments", $data);
		return $this->db->insert_ID();
	}

	public function get_comment($id) 
	{
		return $this->db
			->where("feed_item_comments.ID", $id)
			->select("feed_item_comments.ID, feed_item_comments.postid,
				feed_item_comments.userid, feed_item_comments.comment,
				feed_item_comments.timestamp, feed_item_comments.likes,
				feed_item_comments.commentid, feed_item_comments.replies,
				feed_item.comments, feed_item.ID as feeditemid,
				users.ID as userid, users.username, users.email, users.email_notification,
				fc.postid as fcpostid, fc_item.comments as fc_item_comments")
			->join("feed_item", "feed_item.ID = feed_item_comments.postid", "left outer")
			->join("feed_item_comments as fc", "fc.ID = feed_item_comments.commentid", "left outer")
			->join("feed_item as fc_item", "fc_item.ID = fc.postid", "left outer")
			->join("users", "users.ID = feed_item_comments.userid")
			->get("feed_item_comments");
	}

	public function increment_post($id) {
		$this->db->where("ID", $id)->set("comments", "comments+1", FALSE)->update("feed_item");
	}

	public function get_comment_like($id, $userid) 
	{
		return $this->db->where("commentid", $id)
			->where("userid", $userid)->get("feed_item_comment_likes");
	}

	public function update_comment($id, $data) 
	{
		$this->db->where("ID", $id)->update("feed_item_comments", $data);
	}

	public function delete_comment_like($id) 
	{
		$this->db->where("ID", $id)->delete("feed_item_comment_likes");
	}

	public function add_comment_like($data) 
	{
		$this->db->insert("feed_item_comment_likes", $data);
	}

	public function get_comment_replies($id, $userid, $page) 
	{
		return $this->db
			->where("feed_item_comments.commentid", $id)
			->select("feed_item_comments.ID, feed_item_comments.commentid, feed_item_comments.timestamp, feed_item_comments.comment,
				feed_item_comments.userid, feed_item_comments.likes,
				users.username, users.first_name, users.last_name, users.online_timestamp,
				users.avatar,
				feed_item_comment_likes.ID as commentlikeid")
			->join("users", "users.ID = feed_item_comments.userid")
			->join("feed_item_comment_likes", "feed_item_comment_likes.commentid = feed_item_comments.ID AND feed_item_comment_likes.userid = " . $userid, "LEFT OUTER")
			->order_by("ID", "DESC")
			->get("feed_item_comments");
	}

	public function add_feed_users($data) 
	{
		$this->db->insert("feed_item_users", $data);
	}

	public function get_feed_users($id) 
	{
		return $this->db->where("feed_item_users.postid", $id)
			->select("users.username, users.first_name, users.last_name")
			->join("users", "users.ID = feed_item_users.userid")
			->get("feed_item_users");
	}

	public function get_feed_item($id) 
	{
		return $this->db->where("ID", $id)
			->select("*")
			->get("feed_item");
	}

	public function delete_feed_users($id) {
		$this->db->where("postid", $id)->delete("feed_item_users");
	}

	public function add_feed_image($data) 
	{
		$this->db->insert("feed_item_images", $data);
	}

	public function get_feed_images($id) 
	{
		return $this->db
			->where("feed_item_images.postid", $id)
			->select("user_images.file_name, user_images.ID as imageid,
				user_images.file_url, user_images.name, user_images.description,
				user_albums.ID as albumid, user_albums.name as album_name")
			->join("user_images", "user_images.ID = feed_item_images.imageid")
			->join("user_albums", "user_albums.ID = user_images.albumid")
			->get("feed_item_images");
	}

	public function add_hashtag($data) 
	{
		$this->db->insert("feed_hashtags", $data);
	}

	public function get_hashtag($tag) 
	{
		return $this->db->where("hashtag", $tag)->get("feed_hashtags");
	}

	public function increment_hashtag($id) 
	{
		$this->db->where("ID", $id)->set("count", "count+1", FALSE)->update("feed_hashtags");
	}

	public function get_trending_hashtags($limit) 
	{
		return $this->db->limit($limit)->order_by("COUNT", "DESC")->get("feed_hashtags");
	}

	public function get_user_save_post($id, $userid) 
	{
		return $this->db->where("postid", $id)->where("userid", $userid)->get("user_saved_posts");
	}

	public function add_saved_post($data) 
	{
		$this->db->insert("user_saved_posts", $data);
	}

	public function delete_saved_post($id) 
	{
		$this->db->where("ID", $id)->delete("user_saved_posts");
	}

	public function add_tagged_user($data) 
	{
		$this->db->insert("feed_tagged_users", $data);
	}

	public function get_feed_tag($id, $userid) 
	{
		return $this->db->where("postid", $id)->where("userid", $userid)->get("feed_tagged_users");
	}

	public function add_feed_subscriber($data) 
	{
		$this->db->insert("feed_item_subscribers", $data);
	}

	public function get_feed_subscriber($id, $userid) 
	{
		return $this->db->where("postid", $id)->where("userid", $userid)->get("feed_item_subscribers");
	}

	public function get_feed_subscribers($id) 
	{
		return $this->db
			->where("postid", $id)
			->select("users.ID, users.username, users.email, users.email_notification")
			->join("users", "users.ID = feed_item_subscribers.userid", "left outer")
			->get("feed_item_subscribers");
	}

	public function delete_feed_subscribe($id) 
	{
		$this->db->where("ID", $id)->delete("feed_item_subscribers");
	}

	public function delete_comment($id) 
	{
		$this->db->where("ID", $id)->delete("feed_item_comments");
	}

	public function add_feed_site($data) 
	{
		$this->db->insert("feed_item_urls", $data);
	}

	public function get_feed_urls($id) 
	{
		return $this->db->where("postid", $id)->get("feed_item_urls");
	}

	public function add_user_subscriber($data) 
	{
		$this->db->insert("user_subscribers", $data);
	}

	public function delete_user_subscriber($id) 
	{
		$this->db->where("ID", $id)->delete("user_subscribers");
	}

	public function get_user_subscriber($id, $userid) 
	{
		return $this->db->where("friendid", $id)->where("userid", $userid)->get("user_subscribers");
	}

	public function get_my_subscriber($userid) 
	{
		return $this->db->where("friendid", $userid)->get("user_subscribers");
	}

	public function increase_subs($id) {
		$this->db->where("ID", $id)->set("subscribers", "subscribers+1", FALSE)->update("users");
	}

	public function decrease_subs($id) {
		$this->db->where("ID", $id)->set("subscribers", "subscribers-1", FALSE)->update("users");
	}

	public function increase_shares($id) {
		$this->db->where("ID", $id)->set("shares", "shares+1", FALSE)->update("feed_item");
	}

	public function decrease_shares($id) {
		$this->db->where("ID", $id)->set("shares", "shares-1", FALSE)->update("feed_item");
	}
	
	public function check_feed_item($id,$userid) 
	{
		return $this->db->where("userid", $userid)->where("share_pageid", $id)->get("feed_item");
	}



}

?>