<?php
//Kodlayan : Gürkan Ersan
//Keykubad
//Hostgrup İnternet ve Bilişim Hizmetleri
//www.hostgrup.com www.ekonomikhost.net www.hostingal.com
if (!defined("WHMCS")) die("This file cannot be accessed directly");
	
function Yenilenmeyen_Domain_Listesi_config() {
	global $CONFIG;
    $configarray = array(
    "name" => "Yenilenmeyen Domain Listesi",
    "description" => "Bu eklenti Yenileme ödemesi yapılmış ancak işlemi yapılmayan domainleri hem mail atacak hemde panel anasayfasında widget olarak gösterecektir.",
    "version" => "Final",
    "author" => "Hostgrup",
	"fields" => array(
        "yenilenmeyen_apiuser" => array ("FriendlyName" => "Whmcs Api User", "Type" => "text", "Size" => "25",
                              "Description" => "WHMCS Api kullanıcınızı yazınız.", "Default" => "apiuser", ),
        "yenilenmeyen_apisifre" => array ("FriendlyName" => "Whmcs Api Şifre", "Type" => "password", "Size" => "25",
                              "Description" => "WHMCS APİ Secret", ),
		"yenilenmeyen_emailtemp" => array ("FriendlyName" => "Whmcs Email Template", "Type" => "text", "Size" => "25",
                              "Description" => "WHMCS email template adını yazınız", ),
       
    ));

    return $configarray;

}

function Yenilenmeyen_Domain_Listesi_output($vars) {
		echo '<div class="alert alert-info" role="alert">Ödemesi yapılmış ancak yenilemesi yapılmamış domainleri size mail atar. El ile başlatmak için butona basınız.</div>
<form method="post">
<div class="row">
                        <div class="col-md-12">
						<div class="form-group">
						

 
 </div>
 
 </div>
 <p><center><button type="submit" name="baslat" class="btn btn-primary">İşlemi Başlat</button></center></p>
  </form>
 </div>
 </div>';
 
 if(isset($_POST["baslat"])){
    $modulelink = $vars['modulelink'];
	$LANG = $vars['_lang'];
	$apiuser_cek = $vars['yenilenmeyen_apiuser'];
	$apisifre_cek = $vars['yenilenmeyen_apisifre'];
	$yenilenmeyen_emailtemp = $vars['yenilenmeyen_emailtemp'];
	$url	= mysql_fetch_array(mysql_query("SELECT * FROM  tblconfiguration where setting='Domain'"));
	$domain_cek=$url['value'];

		$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, ''.$domain_cek.'/includes/api.php');
						curl_setopt($ch, CURLOPT_POST, 1);
	$domainliste		=mysql_query("SELECT * FROM  tbldomains WHERE status IN ('Active','Expired','Grace')");
		while($domain_yaz=mysql_fetch_array($domainliste)){
			$bitis_zamani	=$domain_yaz["expirydate"];
			$sonraki_odeme	=$domain_yaz["nextduedate"];

				if($bitis_zamani<$sonraki_odeme){
					$alan_ad = $domain_yaz["domain"];
					$alan_aduseri = $domain_yaz["userid"];
					$domain_useri =mysql_query("SELECT * FROM  tblclients WHERE id='".$alan_aduseri."'");
					$yaz_kullanicisi	=mysql_fetch_array($domain_useri);
					$mailbilgiat.= $yaz_kullanicisi["firstname"].' '.$yaz_kullanicisi["lastname"].'-'.$alan_ad.'<br>';
					

					
					
				}
				if($bitis_zamani>$sonraki_odeme){
					$alan_adlari = $domain_yaz["domain"];
					$alan_aduser = $domain_yaz["userid"];
					$domain_user =mysql_query("SELECT * FROM  tblclients WHERE id='".$alan_aduser."'");
					$yaz_kullanici	=mysql_fetch_array($domain_user);
					$mailbilgisi.=$yaz_kullanici["firstname"].' '.$yaz_kullanici["lastname"].'-'.$alan_adlari.'<br>';
					

					
					
				}
			
		}
					curl_setopt($ch, CURLOPT_POSTFIELDS,
						http_build_query(
							array(
								'action' => 'SendAdminEmail',
								'username' => $apiuser_cek,
								'password' => $apisifre_cek,
								'messagename' => $yenilenmeyen_emailtemp,
								'custommessage' => '<b>Ödemesi yapılmış yenileme yapılmamış domainler</b><br>'.$mailbilgiat.'<b>Ödemesi yapılmamış yenileme yapılmış domainler</b><br>'.$mailbilgisi.'',
								'customsubject' =>'Ödemesi Yapılmış Yenileme Yapılmamış Domainler',
								'responsetype' => 'json',
							)
						)
					);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$mailat = curl_exec($ch);
					return $mailat;
	
 

}
}
   



?>