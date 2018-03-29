<?

include '/var/www/html/ams/modules/autocall/call.php';


function digit($digit,$kop){
    if ($digit>999999) return "error";
    if ($kop>99) return "error";    
    $out_file_wav="/tmp/1.wav";
    $out_file_gsm="/var/lib/asterisk/sounds/autocall/out_test.gsm";
    $digit_path="/var/lib/asterisk/sounds/digit";
    $str_buf;

    $kd = $kop % 100;
    $ke = $kop % 10;


    //копейки
    if (($kd<10)||($kd>=20)){    
	switch ($ke) {
	    case 0: $str_buf = " kopeek.wav ";break;
	    case 1: $str_buf = "odna.wav kopeika.wav ";break;
	    case 2: $str_buf = "dve.wav kopeiki.wav ";break;
	    case 3: $str_buf = "3.wav kopeiki.wav";break;
	    case 4: $str_buf = "4.wav kopeiki.wav";break;
	    case 5: $str_buf = "5.wav kopeek.wav";break;
	    case 6: $str_buf = "6.wav kopeek.wav";break;
	    case 7: $str_buf = "7.wav kopeek.wav";break;
	    case 8: $str_buf = "8.wav kopeek.wav";break;
	    case 9: $str_buf = "9.wav kopeek.wav";break;
	};
    };
    if (($kd>=10) &&($kd<20)){
	switch ($kd) {
	    case 10: $str_buf = "10.wav kopeek.wav";break;
	    case 11: $str_buf = "11.wav kopeek.wav";break;	
	    case 12: $str_buf = "12.wav kopeek.wav";break;
	    case 13: $str_buf = "13.wav kopeek.wav";break;
	    case 14: $str_buf = "14.wav kopeek.wav";break;
	    case 15: $str_buf = "15.wav kopeek.wav";break;
	    case 16: $str_buf = "16.wav kopeek.wav";break;
	    case 17: $str_buf = "17.wav kopeek.wav";break;
	    case 18: $str_buf = "18.wav kopeek.wav";break;
	    case 19: $str_buf = "19.wav kopeek.wav";break;
	};
    }else if ($kd>=20){
	switch(intval($kd/10)) {
	    case 2: $str_buf = "20.wav ".$str_buf;break;
	    case 3: $str_buf = "30.wav ".$str_buf;break;
	    case 4: $str_buf = "40.wav ".$str_buf;break;
	    case 5: $str_buf = "50.wav ".$str_buf;break;
	    case 6: $str_buf = "60.wav ".$str_buf;break;
	    case 7: $str_buf = "70.wav ".$str_buf;break;
	    case 8: $str_buf = "80.wav ".$str_buf;break;
	    case 9: $str_buf = "90.wav ".$str_buf;break;

	}
    };

//основная сумма
    $t = intval($digit/1000);
    $th = intval($t/100);
    $td = $t % 100;
    $te = $t % 10;    

    $h = intval(($digit % 1000)/100);//возьмём только сотни
    $d = $digit % 100;
    $e = $digit % 10;
    if (($d<10)||($d>=20)){    
	switch ($e) {
	    case 0: $str_buf = " rublei.wav ".$str_buf;break;
	    case 1: $str_buf = "1.wav rubl.wav ".$str_buf;break;
	    case 2: $str_buf = "2.wav rublya.wav ".$str_buf;break;
	    case 3: $str_buf = "3.wav rublya.wav ".$str_buf;break;
	    case 4: $str_buf = "4.wav rublya.wav ".$str_buf;break;
	    case 5: $str_buf = "5.wav rublei.wav ".$str_buf;break;
	    case 6: $str_buf = "6.wav rublei.wav ".$str_buf;break;
	    case 7: $str_buf = "7.wav rublei.wav ".$str_buf;break;
	    case 8: $str_buf = "8.wav rublei.wav ".$str_buf;break;
	    case 9: $str_buf = "9.wav rublei.wav ".$str_buf;break;
	};
    };
    if (($d>=10) &&($d<20)){
	switch ($d) {
	    case 10: $str_buf = "10.wav rublei.wav ".$str_buf;break;
	    case 11: $str_buf = "11.wav rublei.wav ".$str_buf;break;	
	    case 12: $str_buf = "12.wav rublei.wav ".$str_buf;break;
	    case 13: $str_buf = "13.wav rublei.wav ".$str_buf;break;
	    case 14: $str_buf = "14.wav rublei.wav ".$str_buf;break;
	    case 15: $str_buf = "15.wav rublei.wav ".$str_buf;break;
	    case 16: $str_buf = "16.wav rublei.wav ".$str_buf;break;
	    case 17: $str_buf = "17.wav rublei.wav ".$str_buf;break;
	    case 18: $str_buf = "18.wav rublei.wav ".$str_buf;break;
	    case 19: $str_buf = "19.wav rublei.wav ".$str_buf;break;
	};
    }else if ($d>=20){
	switch(intval($d/10)) {
	    case 2: $str_buf = "20.wav ".$str_buf;break;
	    case 3: $str_buf = "30.wav ".$str_buf;break;
	    case 4: $str_buf = "40.wav ".$str_buf;break;
	    case 5: $str_buf = "50.wav ".$str_buf;break;
	    case 6: $str_buf = "60.wav ".$str_buf;break;
	    case 7: $str_buf = "70.wav ".$str_buf;break;
	    case 8: $str_buf = "80.wav ".$str_buf;break;
	    case 9: $str_buf = "90.wav ".$str_buf;break;

	}
    };
    //сотни
    switch ($h) {
	case 1: $str_buf = "100.wav ".$str_buf;break;
	case 2: $str_buf = "200.wav ".$str_buf;break;
	case 3: $str_buf = "300.wav ".$str_buf;break;
	case 4: $str_buf = "400.wav ".$str_buf;break;
	case 5: $str_buf = "500.wav ".$str_buf;break;
	case 6: $str_buf = "600.wav ".$str_buf;break;
	case 7: $str_buf = "700.wav ".$str_buf;break;
	case 8: $str_buf = "800.wav ".$str_buf;break;
	case 9: $str_buf = "900.wav ".$str_buf;break;

    };
    //тысячи
    if ($digit>1000) {
	if (($td<10)||($td>=20)){
	    switch ($te) {
		case 0: $str_buf = "1000h.wav ".$str_buf;break;
		case 1: $str_buf = "odna.wav 1000a.wav ".$str_buf;break;
		case 2: $str_buf = "dve.wav 1000i.wav ".$str_buf;break;
		case 3: $str_buf = "3.wav 1000i.wav ".$str_buf;break;
		case 4: $str_buf = "4.wav 1000i.wav ".$str_buf;break;
		case 5: $str_buf = "5.wav 1000h.wav ".$str_buf;break;
		case 6: $str_buf = "6.wav 1000h.wav ".$str_buf;break;
		case 7: $str_buf = "7.wav 1000h.wav ".$str_buf;break;
		case 8: $str_buf = "8.wav 1000h.wav ".$str_buf;break;
		case 9: $str_buf = "9.wav 1000h.wav ".$str_buf;break;
	    };
	};
	if (($td>=10) &&($td<20)){
	    switch ($td) {
		case 10: $str_buf = "10.wav 1000h.wav ".$str_buf;break;
		case 11: $str_buf = "11.wav 1000h.wav ".$str_buf;break;	
		case 12: $str_buf = "12.wav 1000h.wav ".$str_buf;break;
		case 13: $str_buf = "13.wav 1000h.wav ".$str_buf;break;
		case 14: $str_buf = "14.wav 1000h.wav ".$str_buf;break;
		case 15: $str_buf = "15.wav 1000h.wav ".$str_buf;break;
		case 16: $str_buf = "16.wav 1000h.wav ".$str_buf;break;
		case 17: $str_buf = "17.wav 1000h.wav ".$str_buf;break;
		case 18: $str_buf = "18.wav 1000h.wav ".$str_buf;break;
		case 19: $str_buf = "19.wav 1000h.wav ".$str_buf;break;
	    };
	}else if ($td>=20){
	    switch(intval($td/10)) {
		case 2: $str_buf = "20.wav ".$str_buf;break;
		case 3: $str_buf = "30.wav ".$str_buf;break;
		case 4: $str_buf = "40.wav ".$str_buf;break;
		case 5: $str_buf = "50.wav ".$str_buf;break;
		case 6: $str_buf = "60.wav ".$str_buf;break;
		case 7: $str_buf = "70.wav ".$str_buf;break;
		case 8: $str_buf = "80.wav ".$str_buf;break;
		case 9: $str_buf = "90.wav ".$str_buf;break;
	    }
	};
	//сотни
	switch ($th) {
	    case 1: $str_buf = "100.wav ".$str_buf;break;
	    case 2: $str_buf = "200.wav ".$str_buf;break;
	    case 3: $str_buf = "300.wav ".$str_buf;break;
	    case 4: $str_buf = "400.wav ".$str_buf;break;
	    case 5: $str_buf = "500.wav ".$str_buf;break;
	    case 6: $str_buf = "600.wav ".$str_buf;break;
	    case 7: $str_buf = "700.wav ".$str_buf;break;
	    case 8: $str_buf = "800.wav ".$str_buf;break;
	    case 9: $str_buf = "900.wav ".$str_buf;break;
	};
    };



    return $str_buf;
};

function call_with_summ($digit,$kop,$number,$filename,$wait,$id){

    $file_name = " /var/lib/asterisk/sounds/autocall/call_with_summ/".round(microtime(true) * 1000);
    exec("cd /var/lib/asterisk/sounds/digit && /usr/bin/sox ".digit($digit,$kop)." ".$file_name.".wav");
    exec("/usr/bin/sox ".$file_name.".wav -r 8000 ".$file_name.".gsm");
    exec("/usr/bin/sox ".$filename." ".$file_name.".gsm ".$file_name."_out.gsm");
    return call($number,$file_name."_out.gsm",$wait,$id);
    

}

//echo call_with_summ(100,1,'508','/var/lib/asterisk/sounds/autocall/Urk.gsm',100,233321123);

//exec("cd /var/lib/asterisk/sounds/digit");
//for ($i=123;$i<=200000;$i+=1000){
//    echo digit($i)."\n";
//    exec("cd /var/lib/asterisk/sounds/digit && /usr/bin/sox out.wav ".digit($i)." out_tmp.wav");
//    exec("cd /var/lib/asterisk/sounds/digit && rm out.wav");
//    exec("cd /var/lib/asterisk/sounds/digit && mv out_tmp.wav out.wav");
//};

?>
