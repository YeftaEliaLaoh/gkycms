<?php 
    function cetak($str){
        return strip_tags(htmlentities($str, ENT_QUOTES, 'UTF-8'));
    }

    function cetak_meta($str,$mulai,$selesai){
        return strip_tags(html_entity_decode(substr($str,$mulai,$selesai), ENT_COMPAT, 'UTF-8'));
    }
	
	function foto_produk($fot){
        if(!empty($fot)) {
			$val = "asset/foto_produk/$fot";
		} else {
			$val = "asset/cji_logo.png";
		}
        return $val;
    }
    
    function cdate($timestamp) {
        return date("d/m/Y",$timestamp);
    }
    function sensor($teks){
        $ci = & get_instance();
        $query = $ci->db->query("SELECT * FROM katajelek");
        foreach ($query->result_array() as $r) {
            $teks = str_replace($r['kata'], $r['ganti'], $teks);       
        }
        return $teks;
    }  

    function getSearchTermToBold($text, $words){
        preg_match_all('~[A-Za-z0-9_äöüÄÖÜ]+~', $words, $m);
        if (!$m)
            return $text;
        $re = '~(' . implode('|', $m[0]) . ')~i';
        return preg_replace($re, '<b style="color:red">$0</b>', $text);
    }

    function alphabet($data) {
        $alpha = array('','A','B','C','D','E','F','G','H','I','J','K', 'L','M','N','O','P','Q','R','S','T','U','V','W','X ','Y','Z');

        return $alpha[$data];
    }

    function tgl_report($tgl){
        $hari = date ("D",strtotime($tgl));

        $tanggal = substr($tgl,8,2);
        $bulan = getBulann(substr($tgl,5,2));
        $tahun = substr($tgl,0,4);
     
        switch($hari){
            case 'Sun':
                $hari_ini = "Minggu";
            break;
     
            case 'Mon':			
                $hari_ini = "Senin";
            break;
     
            case 'Tue':
                $hari_ini = "Selasa";
            break;
     
            case 'Wed':
                $hari_ini = "Rabu";
            break;
     
            case 'Thu':
                $hari_ini = "Kamis";
            break;
     
            case 'Fri':
                $hari_ini = "Jumat";
            break;
     
            case 'Sat':
                $hari_ini = "Sabtu";
            break;
            
            default:
                $hari_ini = "Tidak di ketahui";		
            break;
        }
        $tgl_terbilang = terbilang($tanggal);
        $tahun_terbilang = terbilang($tahun);
     
        return $hari_ini.", tanggal $tanggal ($tgl_terbilang) $bulan Tahun $tahun ($tahun_terbilang)";
     
    }

    function romawi($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    function tgl_indo($tgl){
            $tanggal = substr($tgl,8,2);
            $bulan = getBulan(substr($tgl,5,2));
            $tahun = substr($tgl,0,4);
            return $tanggal.' '.$bulan.' '.$tahun;       
    } 
	
	function harga($val){
			if(!empty($val)) {
				$harga = 'Rp. '.number_format($val,0,',','.');
            } else {
				$harga = 'Rp. 0';
			}
			return $harga;       
    } 

    function harga_view($val){
        if(!empty($val)) {
            $harga = number_format($val,0,'.',',');
        } else {
            $harga = 0;
        }
        return $harga;       
} 
/*
    function harga_remove_rp($val) {
        $c = array ('Rp','.');
        $value = str_replace($c, '', $val); // Hilangkan karakter yang telah disebutkan di array $d
        return $value;
    }
    */

    function harga_remove_rp($val) {
        $c = array ('Rp',',');
        $value = str_replace($c, '', $val); // Hilangkan karakter yang telah disebutkan di array $d
        return $value;
    }
    
    
    function remove_quote($val) {
        
        $value = trim($val,"'");
        /*
        $c = array ("'");
        $value = str_replace($c, '', $val); // Hilangkan karakter yang telah disebutkan di array $d
        */
        return $value;
    }
    
    function harga_tk($val){
			if(!empty($val)) {
				$harga = 'Rp'.number_format($val,0,',','.');
            } else {
				$harga = 'Rp0';
			}
			return $harga;       
    } 

    function harga_print($val){
        if(!empty($val)) {
            $harga = number_format($val,0,',','.');
        } else {
            $harga = '-';
        }
        return $harga;       
    } 

    function tgl_indoo($tgl){
            $tanggal = substr($tgl,8,2);
            $bulan = getBulann(substr($tgl,5,2));
            $tahun = substr($tgl,0,4);
            return $tanggal.' '.$bulan.' '.$tahun;       
    } 

    function format_time($tgl) {
        $time = substr($tgl,11,5);

        return $time;
    }

    function tgl_simpan2($tgl){
        $bulan = substr($tgl,0,2);
        $tanggal = substr($tgl,3,2);
        $tahun = substr($tgl,6,4);
        return $tahun.'-'.$bulan.'-'.$tanggal;       
    }

    function tgl_simpan($tgl){
            $tanggal = substr($tgl,0,2);
            $bulan = substr($tgl,3,2);
            $tahun = substr($tgl,6,4);
            return $tahun.'-'.$bulan.'-'.$tanggal;       
    }

    function tgl_my($tgl){
        $tahun = substr($tgl,3,4);
        $bulan = substr($tgl,0,2);
        return $tahun.'-'.$bulan;       
    }

    function tgl_my2($tgl){
        $tahun = substr($tgl,0,4);
        $bulan = substr($tgl,4,2);
        return $tahun.'-'.$bulan;       
    }

    function tgl_viewmy($tgl){
        $tahun = substr($tgl,0,4);
        $bulan = substr($tgl,5,2);
        return $bulan.'-'.$tahun;         
    }

    function tgl_viewmyindo($tgl){
        $tahun = substr($tgl,0,4);
        $bulan = getBulann(substr($tgl,5,2));
        return $bulan.'-'.$tahun;         
    }

    function tgl_viewmyeng($tgl){
        $tahun = substr($tgl,0,4);
        $bulan = substr($tgl,4,2);
        $bulan = date('F', mktime(0, 0, 0, $bulan, 10));
        return $bulan.' '.$tahun;         
    }

    function tgl_viewmyeng2($tgl){
        $tanggal = substr($tgl,0,2);
        $bulan = substr($tgl,3,2);
        $tahun = substr($tgl,6,4);

        return $tahun.$bulan;         
    }

    function tgl_view2($tgl){
        $tanggal = substr($tgl,8,2);
        $bulan = substr($tgl,5,2);
        $tahun = substr($tgl,0,4);
        return $bulan.'/'.$tanggal.'/'.$tahun;       
    }

    function tgl_view($tgl){
        if(!empty($tgl)) {
            $tanggal = substr($tgl,8,2);
            $bulan = substr($tgl,5,2);
            $tahun = substr($tgl,0,4);
            $str = $tanggal.'/'.$bulan.'/'.$tahun; 
        } else {
            $str = "";
        }
            
            return $str;      
    }

    function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim(penyebut($nilai));
		} else {
			$hasil = trim(penyebut($nilai));
		}     		
		return ucwords($hasil);
	}

    function tgl_grafik($tgl){
            $tanggal = substr($tgl,8,2);
            $bulan = getBulan(substr($tgl,5,2));
            $tahun = substr($tgl,0,4);
            return $tanggal.'_'.$bulan;       
    }   

    function generateRandomString($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    } 

    function hari_ini(){
        date_default_timezone_set('Asia/Jakarta'); // PHP 6 mengharuskan penyebutan timezone.
        $seminggu = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
        $hari = date("w");
        return $seminggu[$hari];
    }

    function seo_title($s) {
        $c = array (' ');
        $d = array ('-','/','\\',',','.','#',':',';','\'','"','[',']','{','}',')','(','|','`','~','!','@','%','$','^','&','*','=','?','+','–');
        $s = str_replace($d, '', $s); // Hilangkan karakter yang telah disebutkan di array $d
        $s = strtolower(str_replace($c, '-', $s)); // Ganti spasi dengan tanda - dan ubah hurufnya menjadi kecil semua
        return $s;
    }

    function getTahun($val = null){
        $year = date("Y");
        $before = $year-4;
        $after = $year+1;
        $opt = "";
        $sel = "";
        for($i = $before; $i <= $after;$i++) {
            if(!empty($val)) { 
                if($val == $i) { 
                    $sel = "selected='selected'"; 
                } else { $sel = ''; } 
            } else {
                if($year == $i) {
                    $sel = "selected='selected'";
                } else {
                    $sel = "";
                }
            }
            $opt .= "<option $sel value='$i'>$i</option>";
        }

        return $opt;
    }

    function getBulan($bln){
                switch ($bln){
                    case 1: 
                        return "Jan";
                        break;
                    case 2:
                        return "Feb";
                        break;
                    case 3:
                        return "Mar";
                        break;
                    case 4:
                        return "Apr";
                        break;
                    case 5:
                        return "Mei";
                        break;
                    case 6:
                        return "Jun";
                        break;
                    case 7:
                        return "Jul";
                        break;
                    case 8:
                        return "Agu";
                        break;
                    case 9:
                        return "Sep";
                        break;
                    case 10:
                        return "Okt";
                        break;
                    case 11:
                        return "Nov";
                        break;
                    case 12:
                        return "Des";
                        break;
                }
            } 

    function getBulann($bln){
                switch ($bln){
                    case 1: 
                        return "Januari";
                        break;
                    case 2:
                        return "Februari";
                        break;
                    case 3:
                        return "Maret";
                        break;
                    case 4:
                        return "April";
                        break;
                    case 5:
                        return "Mei";
                        break;
                    case 6:
                        return "Juni";
                        break;
                    case 7:
                        return "Juli";
                        break;
                    case 8:
                        return "Agustus";
                        break;
                    case 9:
                        return "September";
                        break;
                    case 10:
                        return "Oktober";
                        break;
                    case 11:
                        return "November";
                        break;
                    case 12:
                        return "Desember";
                        break;
                }
            }

function cek_terakhir($datetime, $full = false) {
	 $today = time();    
     $createdday= strtotime($datetime); 
     $datediff = abs($today - $createdday);  
     $difftext="";  
     $years = floor($datediff / (365*60*60*24));  
     $months = floor(($datediff - $years * 365*60*60*24) / (30*60*60*24));  
     $days = floor(($datediff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
     $hours= floor($datediff/3600);  
     $minutes= floor($datediff/60);  
     $seconds= floor($datediff);  
     //year checker  
     if($difftext=="")  
     {  
       if($years>1)  
        $difftext=$years." Tahun";  
       elseif($years==1)  
        $difftext=$years." Tahun";  
     }  
     //month checker  
     if($difftext=="")  
     {  
        if($months>1)  
        $difftext=$months." Bulan";  
        elseif($months==1)  
        $difftext=$months." Bulan";  
     }  
     //month checker  
     if($difftext=="")  
     {  
        if($days>1)  
        $difftext=$days." Hari";  
        elseif($days==1)  
        $difftext=$days." Hari";  
     }  
     //hour checker  
     if($difftext=="")  
     {  
        if($hours>1)  
        $difftext=$hours." Jam";  
        elseif($hours==1)  
        $difftext=$hours." Jam";  
     }  
     //minutes checker  
     if($difftext=="")  
     {  
        if($minutes>1)  
        $difftext=$minutes." Menit";  
        elseif($minutes==1)  
        $difftext=$minutes." Menit";  
     }  
     //seconds checker  
     if($difftext=="")  
     {  
        if($seconds>1)  
        $difftext=$seconds." Detik";  
        elseif($seconds==1)  
        $difftext=$seconds." Detik";  
     }  
     return $difftext;  
	}