<?php
if (!defined("WHMCS")) die("This file cannot be accessed directly");
add_hook('DailyCronJob', 1, function () {


	//config kısmında yazılan degerleri alıyoruz
	$url	= mysql_fetch_array(mysql_query("SELECT * FROM  tblconfiguration where setting='Domain'"));
	$domain_cek=$url['value'];
	$apiuser	= mysql_fetch_array(mysql_query("SELECT * FROM  tbladdonmodules where setting='yenilenmeyen_apiuser'"));
	$apiuser_cek=$apiuser['value'];
	$apisifre	= mysql_fetch_array(mysql_query("SELECT * FROM  tbladdonmodules where setting='yenilenmeyen_apisifre'"));
	$apisifre_cek=$apisifre['value'];
	$yenilenmeyen_emailtemp	= mysql_fetch_array(mysql_query("SELECT * FROM  tbladdonmodules where setting='yenilenmeyen_emailtemp'"));
	$yenilenmeyen_emailtemp_cek=$yenilenmeyen_emailtemp['value'];

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
								'messagename' => $yenilenmeyen_emailtemp_cek,
								'custommessage' => '<b>Ödemesi yapılmış yenileme yapılmamış domainler</b><br>'.$mailbilgiat.'<b>Ödemesi yapılmamış yenileme yapılmış domainler</b><br>'.$mailbilgisi.'',
								'customsubject' =>'Ödemesi Yapılmış Yenileme Yapılmamış Domainler',
								'responsetype' => 'json',
							)
						)
					);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$mailat = curl_exec($ch);
					return $mailat;
	
 


	

	
});

?>