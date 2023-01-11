<?php 
    function cek_session_admin(){
        $ci = & get_instance();
        $session = $ci->session->userdata('level');
        if ($session == ''){
            redirect(base_url());
        }
    }
    
    function tokped_token() {
        $ci = & get_instance();
    	$row = $ci->db->query("SELECT * FROM identitas WHERE id_identitas = 1")->row();
    	if(!empty($row->tokped_token)) {
    	    return $row->tokped_token;
    	} else {
    	    return "no token";
    	}
    }

    function have2dec($number) {
        return (preg_match('/\.[0-9]{2,}[1-9][0-9]*$/', (string)$number) > 0);
    }

    function crewdible($token,$path,$post) {
        $post = json_encode($post);
        
        $host = CREWDIBLE_URL.$path;

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/json',
            'Cookie: PHPSESSID=mja3vbvf18s8obi737hegj16dc'
            ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return $response;
    }

    function getUser($id) {
        $ci = & get_instance();
        
    	$c = $ci->db->query("SELECT * FROM users WHERE id = $id")->row();

        return $c;
    }

    function postcurltoken($url,$pst,$token) {
        
		
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => URL_API.$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $pst,
        CURLOPT_HTTPHEADER => array(
            "token: $token"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    function postcurltokenimage($url,$pst,$token,$tempFile) {
       
        $author = "";
        $author1 = "";
        $author2 = "";
        $author3 = "";
        if(!empty($pst['author1'])) {
//            $author .= (int)$pst['author1'].',';
            $author1 = $pst['author1'];
            
        }
        if(!empty($pst['author2'])) {
  //          $author .= (int)$pst['author2'].',';
          $author2 = $pst['author2'];
        }
        if(!empty($pst['author3'])) {
            $author3 = $pst['author3'];
  //         $author .= (int)$pst['author3'].',';
        }

        $arr_author = explode(',',rtrim($author,','));

        $authors = array('author[]'=>$arr_author);

        /*
		$arr = array('title' => $pst['title'],'titleseries' => $pst['titleseries'],'yearpublish' => $pst['yearpublish'],'idlanguage' => $pst['idlanguage'],
        'idpublisher' => $pst['idpublisher'],'idbookcategory'=>$pst['idbookcategory'],'description'=>$pst['description'],'image'=> new CURLFILE($tempFile));

                 $arr = array('title' => $pst['title'],'titleseries' => $pst['titleseries'],'yearpublish' => $pst['yearpublish'],'idlanguage' => $pst['idlanguage'],
        'idpublisher' => $pst['idpublisher'],'idbookcategory'=>$pst['idbookcategory'],'description'=>$pst['description'],'author'=> $arr_author,'image'=> new CURLFILE($tempFile));
        */
        if(!empty($tempFile)) {
            $arr = array('title' => $pst['title'],'titleseries' => $pst['titleseries'],'yearpublish' => $pst['yearpublish'],'idlanguage' => $pst['idlanguage'],
            'idpublisher' => $pst['idpublisher'],'idbookcategory'=>$pst['idbookcategory'],'description'=>$pst['description'],
            "author[0]"=>$author1,"author[1]"=>$author2,"author[2]"=>$author3,'image'=> new CURLFILE($tempFile));
        } else {
            $arr = array('title' => $pst['title'],'titleseries' => $pst['titleseries'],'yearpublish' => $pst['yearpublish'],'idlanguage' => $pst['idlanguage'],
            'idpublisher' => $pst['idpublisher'],'idbookcategory'=>$pst['idbookcategory'],'description'=>$pst['description'],
            "author[0]"=>$author1,"author[1]"=>$author2,"author[2]"=>$author3);
        }


        $curl = curl_init();
/*
        curl_setopt_array($curl, array(
        CURLOPT_URL => URL_API.$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>  json_encode($arr,true),
        CURLOPT_HTTPHEADER => array(
            "token: $token"
        ),
        ));
        */
/*
curl_setopt_array($curl, array(
    CURLOPT_URL => URL_API.$url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($arr,true),
    CURLOPT_HTTPHEADER => array(
      "token: $token",
      'Content-Type: application/json'
    ),
  ));
  
        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
        */

        curl_setopt_array($curl, array(
        CURLOPT_URL => URL_API.$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $arr,
        CURLOPT_HTTPHEADER => array(
            "token: $token"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    function poststatus($url,$pst,$token) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $pst,
        CURLOPT_HTTPHEADER => array(
            "token: $token"
        ),
        ));

        $response = curl_exec($curl);

        return $response;
    }




    function postcurl($url,$pst) {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $pst,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
    
    function phonenumber($val) {
        if(!empty($val)) {
            return preg_replace("/[^0-9]/", "",$val);            
        } else {
            return "";
        }

    }

    function post_url($url,$post) {
        $curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $post,
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
    }

    function get_url($url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    function cek_session_akses($link,$id){
    	$ci = & get_instance();
    	$session = $ci->db->query("SELECT * FROM modul,users_modul WHERE modul.id_modul=users_modul.id_modul AND users_modul.id_session='$id' AND modul.link='$link'")->num_rows();
    	if ($session == '0' AND $ci->session->userdata('level') != 'admin'){
    		redirect(base_url().'administrator/home');
    	}
    }

    function showth($column) {
        $data = "";
        foreach($column as $c) {
            $data .= "<th>$c</th>";
        }
        return $data;
    }

    function option_builder($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            if($type == 2) {
                $qry = $option;
            } else {
                $qry = $ci->db->query($option)->result();
            }

        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val) || $type == 2) {
            $opt = "<option value=''></opt>";
        }
        $sel = "";
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->id) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->id'>$q->name</option>";
        }
    	return $opt;
    }

    
    function option_builder2($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            if($type == 2) {
                $qry = $option;
            } else {
                $qry = $ci->db->query($option)->result();
            }

        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val) || $type == 2) {
            $opt = "<option value=''></opt>";
        }
        $sel = "";
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->name) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->id'>$q->name</option>";
        }
    	return $opt;
    }

    
    function option_builder_book($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            if($type == 2) {
                $qry = $option;
            } else {
                $qry = $ci->db->query($option)->result();
            }

        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val) || $type == 2) {
            $opt = "<option value=''></opt>";
        }
        $sel = "";
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->code) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->id'>$q->code</option>";
        }
    	return $opt;
    }

    function option_builder3($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            if($type == 2) {
                $qry = $option;
            } else {
                $qry = $ci->db->query($option)->result();
            }

        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val) || $type == 2) {
            $opt = "<option value=''></opt>";
        }
        $sel = "";
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->book_id) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->book_id'>$q->title</option>";
        }
    	return $opt;
    }

    
    function option_builder4($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            if($type == 2) {
                $qry = $option;
            } else {
                $qry = $ci->db->query($option)->result();
            }

        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val) || $type == 2) {
            $opt = "<option value=''></opt>";
        }
        $sel = "";
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->name) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->id'>$q->name</option>";
        }
    	return $opt;
    }

    function option_builder_array($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            $qry = $ci->db->query($option)->result();
        } else {
            $qry = $option;
        }

        $opt = "";
        if(empty($val)) {
            $opt = "<option value=''>- All -</opt>";
        }
        
        foreach($qry as $q) {
            if(!empty($q->checked)) { $sel = "selected='selected'"; } else { $sel = ''; }
            $opt .= "<option $sel value='$q->id'>$q->name</option>";
        }
    	return $opt;
    }

    function option_builder_noopt($option,$val = "",$type = 0) {
        $ci = & get_instance();
        if(!empty($type)) {
            $qry = $ci->db->query($option)->result();
        } else {
            $qry = $option;
        }

        $opt = "";
        
        foreach($qry as $q) {
            if(!empty($val)) { if($val == $q->id) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$q->id'>$q->nama</option>";
        }
    	return $opt;
    }

    function clickproduct($id,$name) {
        return "<a href='#' onclick='setproduct".'("'.$id.'")'."'>$name</a>";
    }

    function form_builder($column) {
        $data = "";
        foreach($column as $c) {
            $nama = $c['nama'];
            $type = $c['type'];
            $col = $c['col'];
            $disable = $c['disable'];
            $pst_name = $c['pst_name'];
            $placeholder = $c['placeholder'];
            $id = $c['id'];
            $value = $c['value'];
            $required = $c['required'];
            $option = $c['option'];
            $idhide = $c['idhide'];
            $value_id = $c['value_id'];

            $id_change = $c['id_change'];
            $dsp = $c['display_none'];
            $onchange = $c['onchange'];

            if(empty($disable)) {
                $disable = "";
            }

            $display = "";
            if($dsp == 1) {
                $display = "style='display:none;'";
            }

            if(empty($id)) {
                $id = $pst_name;
            }

            if($type == 'addon') {
                $data .= '<input type="hidden" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>';                
            } 
            else if($type == 'select2remote') {
                $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <select name="'.$pst_name.'" class="form-control js-data-example-ajax" id="'.$id.'" '.$required.'>
                                '.$option.'
                                </select>
                                </div>
                            </div>';
                $data .= "<script>
                $('.js-data-example-ajax').select2({
                    ajax: {
                        url: '".KLOPMARTAPI."products/get_brand_new"."',
                        dataType: 'json'
                        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                    }
                    });
                    </script>";
            }

            else if($type == 'select2remotepublisher') {
                if(!empty($value)) {
                    $option = "<option value=$value_id>$value</option>";
                }
                $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <select name="'.$pst_name.'" class="form-control js-data-publisher" id="'.$id.'" '.$required.'>
                                '.$option.'
                                </select>
                                </div>
                            </div>';
                $data .= "<script>
                $('.js-data-publisher').select2({
                    ajax: {
                        url: '".base_url("master/book/showpublisher")."',
                        dataType: 'json'
                        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                    }
                    });
                    </script>";
            }

            else if($type == 'select2remotecategory') {
                if(!empty($value)) {
                    $option = "<option value=$value_id>$value</option>";
                }
                $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <select name="'.$pst_name.'" class="form-control js-data-category" id="'.$id.'" '.$required.'>
                                '.$option.'
                                </select>
                                </div>
                            </div>';
                $data .= "<script>
                $('.js-data-category').select2({
                    ajax: {
                        url: '".base_url("master/book/showcategory")."',
                        dataType: 'json'
                        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                    }
                    });
                    </script>";
            }

            else if($type == 'select2remoteauthor') {
                if(!empty($value)) {
                    $option = "<option value='$value_id'>$value</option>";
                }
                $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <select name="'.$pst_name.'" class="form-control js-data-example-ajax" id="'.$id.'" '.$required.'>
                                '.$option.'
                                </select>
                                </div>
                            </div>';
                $data .= "<script>
                $('.js-data-example-ajax').select2({
                    ajax: {
                        url: '".base_url("master/book/showauthor")."',
                        dataType: 'json'
                        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                    }
                    });
                    </script>";
            }
            else if($type == 'option') {
               // $opt = $this->option_builder($option,$value);
                $data .= '<div class="col-sm-'.$col.'">
                <div class="form-group">
                <label>'.$nama.'</label>
                <select name="'.$pst_name.'" class="form-control" '.$disable.' id="'.$id.'" '.$required.'>
                '.$option.'
                </select>
                </div>
            </div>';
            }
            else if($type == 'readonly') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="text" readonly="readonly" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                                </div>
                            </div>';
            } 
            else if($type == 'disabled') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="text" disabled="disabled" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                                </div>
                            </div>';
            }
            else if($type == 'empty') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label></label>
                                </div>
                            </div>';
            } 
            else if($type == 'decimal') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="number" step=".01" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                                </div>
                            </div>';
            } 

            else if($type == 'file') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="file" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                                </div>
                            </div>';
                    if(!empty($value)) {
                        $data .= "<div class='col-sm-6'><img src='$value' style='width:150px;'></div>";
                    }
            } 
            
            else if($type == 'file_wimage') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="file" class="form-control" onchange="readURL(this);" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                                
                                </div>
                            </div>';
                        
                    $imga = '<img id="blah" src="'.$value.'" alt="your image" style="width:150px;" />';
                    $data .= "<div class='col-sm-6'>$imga</div>";
                    
            } 
            else if($type == 'maps') {
                // $opt = $this->option_builder($option,$value);
                    $data .= '<div class="col-sm-'.$col.'">
                                <div class="form-group">
                                <label>'.$nama.'</label>
                                <input type="text" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.' onFocus="geolocate()" autocomplete="off">
                                </div>
                            </div>';
            } 
            else if($type == 'select2') {
                // $opt = $this->option_builder($option,$value);
                $data .= '<div '.$display.' id="'.$id_change.'" class="col-sm-'.$col.'">
                <div class="form-group">
                    <label>'.$nama.'</label>
                    <select name="'.$pst_name.'" '.$onchange.' '.$disable.' class="form-control select2bs4" id="'.$id.'" '.$required.'>
                    '.$option.'
                    </select>
                    </div>
                </div>';
             }
             else if($type == 'select2tags') {
                // $opt = $this->option_builder($option,$value);
                 $data .= '<div '.$display.' id="'.$id_change.'" class="col-sm-'.$col.'">
                 <div class="form-group">
                 <label>'.$nama.'</label>
                 <select name="'.$pst_name.'" '.$onchange.' '.$disable.' class="form-control select2bs42" id="'.$id.'" '.$required.'>
                 '.$option.'
                 </select>
                 </div>
             </div>';
             }

             else if($type == 'select2multipleadd') {
                 $ext = $c['ext'];
                // $opt = $this->option_builder($option,$value);
                 $data .= '<div '.$display.' id="'.$id_change.'" class="col-sm-'.$col.'">
                 <div class="form-group">
                 <label>'.$nama.'</label>
                 <select name="'.$pst_name.'"  multiple="multiple" '.$onchange.' class="form-control select2bs4" id="'.$id.'" '.$required.'>
                 '.$option.'
                 </select>
                 '.$ext.'
                 </div>
             </div>';
             }
             else if($type == 'color') {
                // $opt = $this->option_builder($option,$value);
                 $data .= '<div class="col-sm-'.$col.'">
                 <div class="form-group">
                 <label>'.$nama.'</label>
                 <input type="text" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="my-colorpicker1" '.$required.'>
                 </div>
             </div>';
             }
             
            
            else if($type == 'tags') {
                $data .= '<div class="col-sm-'.$col.'">
                <div class="form-group">
                <label>'.$nama.'</label>                
                <input type="text" class="form-control" value="'.$value.'" data-role="tagsinput"  placeholder="'.$placeholder.'" name="'.$pst_name.'" '.$required.'>
                </div>
            </div>';
            }
            else if($type == 'switch') {
                $checked = "";
                if(!empty($value)) {
                    $checked = "checked";
                }
                $data .= '<div class="col-sm-'.$col.'">
                    <div class="form-group">
                    <label>'.$nama.'</label>   
                    <div class="card-body">             
                        <input type="checkbox" value="1" '.$checked.' name="'.$pst_name.'" data-bootstrap-switch >
                    </div>
                    </div>
                </div>';
                
            }
            else if($type == 'showjoborder') {
                // $opt = $this->option_builder($option,$value);
                $data .= '<div class="col-sm-'.$col.'">
                <div class="form-group">
                <label>'.$nama.'</label>
                        <div class="input-group" data-target-input="nearest">
                            <input type="text" value="'.$value.'" class="form-control" id="'.$id.'" name="'.$pst_name.'" '.$required.'/>
                            <div class="input-group-append" id="openmak" data-book-id="1" data-toggle="modal" data-target="#showmodalmak" data-header="List Job Order" data-href="'.base_url("joborder/caridata?type=1").'">
                                <div class="input-group-text"><i class="fa fa-th-large"></i></div>
                            </div>
                        </div>
                </div>
            </div>';
             }
             else if($type == 'divhide') {
                // $opt = $this->option_builder($option,$value);
                $data .= '<div class="col-sm-'.$col.'" id="'.$id.'"></div>';
             }
             else if($type == 'autocomplete') {
                // $opt = $this->option_builder($option,$value);
                if(!empty($idhide)) {
                    $data .= '<div class="col-sm-'.$col.'" id="'.$idhide.'">';
                } else {
                    $data .= '<div class="col-sm-'.$col.'">';
                }


                $data .=  '<div class="form-group">
                 <label>'.$nama.'</label>';
                 $data .= "<input type='text' class='form-control' name='$pst_name' value='$value' id='pencarian_kode2' placeholder='Ketik Kode / Nama Barang'>
				 <div id='hasil_pencarian2'></div>
                 </div>
             </div>";
             }
            else if($type == 'datepicker') {
                if(!empty($idhide)) {
                    $data .= '<div class="col-sm-'.$col.'" id="'.$idhide.'">';
                } else {
                    $data .= '<div class="col-sm-'.$col.'">';
                }


                $data .=  '<div class="form-group">
                        <label>'.$nama.'</label>
                                <div class="input-group date" id="'.$id.'" data-target-input="nearest">
                                    <input type="text" value="'.$value.'" class="form-control datetimepicker-input" name="'.$pst_name.'" data-toggle="datetimepicker" data-target="#'.$id.'" '.$required.'/>
                                    <div class="input-group-append" data-target="#'.$id.'" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                        </div>
                    </div>';
                $data .= "
                <script>
                $(function () {
                    $('#$id').datetimepicker({
                        format: 'DD/MM/YYYY'
                    });
                })
                </script>";
            }
            else if($type == 'datetimepicker') {
                if(!empty($idhide)) {
                    $data .= '<div class="col-sm-'.$col.'" id="'.$idhide.'">';
                } else {
                    $data .= '<div class="col-sm-'.$col.'">';
                }


                $data .=  '<div class="form-group">
                        <label>'.$nama.'</label>
                                <div class="input-group date" id="'.$id.'" data-target-input="nearest">
                                    <input type="text" value="'.$value.'" class="form-control datetimepicker-input" name="'.$pst_name.'" data-toggle="datetimepicker" data-target="#'.$id.'" '.$required.'/>
                                    <div class="input-group-append" data-target="#'.$id.'" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                        </div>
                    </div>';
                $data .= "
                <script>
                $(function () {
                    $('#$id').datetimepicker({
                        format: 'DD/MM/YYYY HH:mm'
                    });
                })
                </script>";
            }
            else if($type == 'textarea') {
                $data .= '<div class="col-sm-'.$col.'">
                <div class="form-group">
                <label>'.$nama.'</label>
                <textarea id="'.$id.'" name="'.$pst_name.'" class="form-control" style="height: 100px" '.$required.' >'.$value.'</textarea>
                </div>
            </div>';
            }
            else if($type == 'texteditor') {
                $data .= '<div class="col-sm-'.$col.'">
                <div class="form-group">
                <label>'.$nama.'</label>
                <textarea id="'.$id.'" name="'.$pst_name.'" class="form-control" style="height: 300px" '.$required.' >'.$value.'</textarea>
                </div>
            </div>';
            $data .= "
                <script>
                $(function () {
                    //Add text editor
                    $('#$id').summernote()
                })
                </script>";
            }
            else {
                if(!empty($idhide)) {
                    $data .= '<div class="col-sm-'.$col.'" id="'.$idhide.'">';
                } else {
                    $data .= '<div class="col-sm-'.$col.'">';
                }


                $data .=  '<div class="form-group" '.$display.' id="'.$id_change.'">
                <label>'.$nama.'</label>
                <input type="'.$type.'" class="form-control" value="'.$value.'" placeholder="'.$placeholder.'" name="'.$pst_name.'" id="'.$id.'" '.$required.'>
                </div>
            </div>';
            }

        }
        return $data;
    }

    function getseqmainmenu($val = null,$total){
    	$ci = & get_instance();
        //$qry = $ci->db->query("SELECT u.*,m.nama_menu as nama FROM user_menu u LEFT JOIN mainmenu m ON m.id_main=u.id WHERE main_user_id = $id_user")->result();
        
        $opt = "<option value=''>- Pilih Menu -</opt>";
        //foreach($qry as $q) {
        for($i=1;$i<=$total;$i++) {
            if(!empty($val)) { if($val == $i) { $sel = "selected='selected'"; } else { $sel = ''; } }
            $opt .= "<option $sel value='$i'>$i</option>";
        }
    	return $opt;
    }

      
      function decimalnumber($val){
        $ci = & get_instance();
       
        $value = str_replace(",", ".", $val);       
        return $value;
    }  

    function updateString($str,$val,$replace) {
        $ci = & get_instance();
       
        $value = str_replace("{".$val."}", $replace, $str);       
        return $value;
    }


    function template(){
        $ci = & get_instance();
        $query = $ci->db->query("SELECT folder FROM templates where aktif='Y'");
        $tmp = $query->row_array();
        if ($query->num_rows()>=1){
           // return $tmp['folder'];
           return "klopmart";
        }else{
            return 'errors';
        }
    }

    function encryptOrder($string) {
        $string = base64_encode($string);
        $integer = '';
        foreach (str_split($string) as $char) {
            $integer .= sprintf("%03s", ord($char));
        }
        return $integer;
    }
    
    function decryptOrder($integer) {
        $string = '';
        foreach (str_split($integer, 3) as $number) {
            $string .= chr($number);
        }
        $string = base64_decode($string);
        return $string;
    }

    function checkGambar($site_url,$val,$path)
    {
        if(!empty($val)) {
            $url = $site_url.$path.$val;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            // don't download content
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
            $result = curl_exec($ch);
            curl_close($ch);
            if($result !== FALSE)
            {
                $banner_img = $url;
            }
            else
            {
                $banner_img = "$site_url/uploads/logo.png";
            }
        } else {
            $banner_img = "$site_url/uploads/logo.png";
        }
        return $banner_img;
    }

    function monthyear($val) {
        if(!empty($val)) {
            $value = date('M Y', strtotime($val));
        } else {
            $value = "";
        }
        return $value;
    }

    function timechat($time) {
        $now = date("Y-m-d");
        $date_chat = date('Y-m-d',  strtotime($time));
        
        $value = date('d/m/Y H:i',  strtotime($time));
        if($now == $date_chat) {
            $value = date('H:i',  strtotime($time));            
        }

        return $value;
    }

    function check_status_payment($status) {
        if($status == 1) {
            $value = "Paid";
            $hex = "blue";
        } else {
            $value = "Outstanding";
            $hex = "red";
        }

        $arr = array("value"=>$value,"hex"=>$hex);

        return $arr;
    }

    function mobile(){
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
            $mobile = 1;
        } else {
            $mobile = 0;
        }
        return $mobile;
    }

    function checkrole($id) {
        if($id == 1) {
            $val = "Admin";
        } 
        else if($id == 2) {
            $val = "User";
        }
        else if($id == 3) {
            $val = "Lead";
        }
        else if($id == 4) {
            $val = "Hods";
        }
        else { 
            $val = "View Only";
        }

        return $val;
    }
