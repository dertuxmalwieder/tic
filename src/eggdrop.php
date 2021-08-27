<?php
#############################################
#											#
#	Tactical Information Center				#
#											#
#	File: eggdrop.php						#
#	Discriptio: File for the Eggdrop- Bot	#
#	Developer: Dennis Stadler				#
#	Dev.-Email: Dennisstadler@kamp-dsl.de	#
#	Date: 06.07.2005							#
#	Version: 1.5							#
#											#
#############################################
#											#
#	Version History:						#
#											#
#	Version 1.1: 	File written			#
#	Version 1.2: 	Bug found and corrected	#
#	Version	1.3: 	Header Written			#
#	Version 1.4: 	Update and 				#
#					include Wurst-script	#
#	Version 1.5:	Fixed scan's date
#											#
#############################################
#											#
#	Coder:									#
#											#
#	Version 1.1: Pretoreaner				#
#	Version 1.2: Pretoreaner				#
#	Version	1.3: Pretoreaner				#
#	Version 1.4: Pretoreaner
#	Version 1.5: Elkano
#											#
#############################################

    include('./accdata.php');
    include('./functions.php');

    $SQL_DBConn = mysqli_connect($db_info['host'], $db_info['user'], $db_info['password']);
    mysqli_select_db($db_info['dbname'], $SQL_DBConn);


    if (!isset($_GET['passwort'])) $_GET['passwort'] = '';
    $SQL_Result0 = tic_mysql_query('SELECT ticid FROM `gn4vars` WHERE name="botpw" AND value="'.$_GET['passwort'].'";', $SQL_DBConn);

    if (mysqli_num_rows($SQL_Result0) != 1) die('Incorrect password');

    $Benutzer['ticid']=tic_mysql_result($SQL_Result0,0,'ticid');

    include('./vars.php');

    $irc_text['fett'] = '';
    $irc_text['unterstrichen'] = '';
    $irc_text['farbe'] = '';
    $irc_farbe['weiss'] = '0';
    $irc_farbe['schwarz'] = '1';
    $irc_farbe['dunkelblau'] = '2';
    $irc_farbe['dunkelgruen'] = '3';
    $irc_farbe['rot'] = '4';
    $irc_farbe['braun'] = '5';
    $irc_farbe['lila'] = '6';
    $irc_farbe['orange'] = '7';
    $irc_farbe['gelb'] = '8';
    $irc_farbe['hellgruen'] = '9';
    $irc_farbe['tuerkise'] = '10';
    $irc_farbe['hellblau'] = '11';
    $irc_farbe['blau'] = '12';
    $irc_farbe['rosa'] = '13';
    $irc_farbe['dunkelgrau'] = '14';
    $irc_farbe['hellgrau'] = '15';

    $irc_listfarbe[0] = $irc_farbe['weiss'];
    $irc_listfarbe[1] = $irc_farbe['hellgrau'];

    include('./globalvars.php');

    $tick_abzug = intval(date('i') / 15);
    $tick_abzug = date('i') - $tick_abzug * 15;


    if (!isset($_GET['modus'])) $_GET['modus'] = 0;

// Alle Atts anzeigen
    if ($_GET['modus'] == 0) {

        $SQL_Result1 = tic_mysql_query('SELECT galaxie, planet, name, allianz FROM `gn4accounts` where ticid="'.$Benutzer['ticid'].'" ORDER BY galaxie, planet;', $SQL_DBConn);
        $SQL_Result2 = tic_mysql_query('SELECT * FROM `gn4flottenbewegungen` WHERE modus="1" && save="1" and ticid="'.$Benutzer['ticid'].'" ORDER BY eta, verteidiger_galaxie, verteidiger_planet;', $SQL_DBConn);

        $SQL_Num1 = mysqli_num_rows($SQL_Result1);
        $SQL_Num2 = mysqli_num_rows($SQL_Result2);

        $text = '';
        $farbe = 0;

        for ($n = 0; $n < $SQL_Num1; $n++) {
            $ziel_galaxie = tic_mysql_result($SQL_Result1, $n, 'galaxie');
            $ziel_planet = tic_mysql_result($SQL_Result1, $n, 'planet');
            $ziel_name = tic_mysql_result($SQL_Result1, $n, 'name');
            $ziel_allianz = $AllianzTag[tic_mysql_result($SQL_Result1, $n, 'allianz')];
            $incomming_counter = 0;

            for ($x = 0; $x < $SQL_Num2; $x++) {
                 $time=tic_mysql_result($SQL_Result2, $x, 'ankunft');
                if ($ziel_galaxie == tic_mysql_result($SQL_Result2, $x, 'verteidiger_galaxie') && $ziel_planet == tic_mysql_result($SQL_Result2, $x, 'verteidiger_planet')) {    // && tic_mysql_result($SQL_Result2, $x, 'eta') >= 18
                    $incomming_counter++;
                    $atter_eta = (eta($time) * 15 - $tick_abzug);
                    if ($incomming_counter == 1) {
                        $etas = $irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.tic_mysql_result($SQL_Result2, $x, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result2, $x, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result2, $x, 'angreifer_galaxie'), tic_mysql_result($SQL_Result2, $x, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.$atter_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].')';
                    } else {
                        $etas = $etas.$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].','.$irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.tic_mysql_result($SQL_Result2, $x, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result2, $x, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result2, $x, 'angreifer_galaxie'), tic_mysql_result($SQL_Result2, $x, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.$atter_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].')';
                    }
                }
            }

            if ($incomming_counter > 0) {
                $text = $text."°".$irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.$ziel_galaxie.':'.$ziel_planet.' ['.$ziel_allianz.'] '.$ziel_name.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].' hat'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_listfarbe[$farbe].' '.$incomming_counter.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_listfarbe[$farbe].' Incomming(s):'.$irc_text['farbe'].$etas;
                if ($farbe == 0)
                    $farbe = 1;
                else
                    $farbe = 0;
            }
        }

        if ($text == '') $text = "°".$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' Es werden momentan keine Verteidiger benötigt.';

// Einzelnen Att anzeigen
    } elseif($_GET['modus'] == 1) {

        if (!isset($_GET['koord']))
            $text = ' Sie mssen eine Koordinate angeben!';
        else {
            $tmp_pos = strpos($_GET['koord'], ':');
            if ($tmp_pos == 0)
                $text = ' Sie mssen eine gltige Koordinate angeben! ('.$_GET['koord'].')';
            else {
                $tmp_galaxie = substr($_GET['koord'], 0, $tmp_pos);
                $tmp_planet = substr($_GET['koord'], $tmp_pos + 1);
                $SQL_Result = tic_mysql_query('SELECT * FROM `gn4flottenbewegungen` WHERE verteidiger_galaxie="'.$tmp_galaxie.'" AND verteidiger_planet="'.$tmp_planet.'" ORDER BY eta, angreifer_galaxie, angreifer_planet;', $SQL_DBConn);
                $incomming_counter = 0;
                $deff_counter = 0;
                $tmp_atter = '';
                $tmp_deffer = '';
                for ($n = 0; $n < mysqli_num_rows($SQL_Result); $n++) {
                    $time=tic_mysql_result($SQL_Result, $n, 'ankunft');
                    if (tic_mysql_result($SQL_Result, $n, 'modus') == 1) {
                        $incomming_counter++;
                        $atter_eta = (eta($time) * 15 - $tick_abzug);
                        if ($incomming_counter == 1) {
                            $tmp_atter = $irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result, $n, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie'), tic_mysql_result($SQL_Result, $n, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$atter_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].')';
                        } else {
                            $tmp_atter = $tmp_atter." 00,01 ".$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result, $n, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie'), tic_mysql_result($SQL_Result, $n, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$atter_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].')';
                        }
                    } elseif (tic_mysql_result($SQL_Result, $n, 'modus') == 2) {
                        $deff_counter++;
                        $deffer_eta = (eta($time) * 15 - $tick_abzug);
                        if ($deff_counter == 1) {
                            $tmp_deffer = $irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result, $n, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie'), tic_mysql_result($SQL_Result, $n, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$deffer_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].')';
                        } else {
                            $tmp_deffer = $tmp_deffer." 00,01 ".$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie').':'.tic_mysql_result($SQL_Result, $n, 'angreifer_planet').$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' ('.GetScans($SQL_DBConn, tic_mysql_result($SQL_Result, $n, 'angreifer_galaxie'), tic_mysql_result($SQL_Result, $n, 'angreifer_planet')).' ETA'.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$deffer_eta.$irc_text['farbe'].$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].')';
                        }
                    }
                }
                $SQL_Result = tic_mysql_query('SELECT * FROM `gn4flottenbewegungen` WHERE verteidiger_galaxie="'.$tmp_galaxie.'" AND verteidiger_planet="'.$tmp_planet.'" ORDER BY verteidiger_galaxie;', $SQL_DBConn);
                $count =  mysqli_num_rows($SQL_Result);
                if ( $count == 0 ){
                    echo 'Der hat kein inc du depp!!!';
                return;
                } else {
                $text = "°".$irc_text['farbe'].$irc_farbe['orange'].','.$irc_farbe['weiss'].' '.gnuser($tmp_galaxie, $tmp_planet).' 01,00(12,00'.$tmp_galaxie.':'.$tmp_planet.'01,00)'.$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' hat'.$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$incomming_counter.$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' Angreifer und'.$irc_text['farbe'].$irc_farbe['blau'].','.$irc_farbe['weiss'].' '.$deff_counter.$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' Verteidiger'."°".$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' Angreifer: '.$tmp_atter."°".$irc_text['farbe'].$irc_farbe['schwarz'].','.$irc_farbe['weiss'].' Verteidiger: '.$tmp_deffer;
        }
            }
        }

    }




// Scans vom Koods anzeigen


elseif($_GET['modus'] == 2) {


				$tmp_pos = strpos($_GET['koord'], ':');

            if ($tmp_pos == 0)

                $text = ' Sie mssen eine gltige Koordinate angeben! ('.$_GET['koord'].')';

				else { $tmp_galaxie = substr($_GET['koord'], 0, $tmp_pos);
                $tmp_planet = substr($_GET['koord'], $tmp_pos + 1);


				$sql='select * from `gn4scans` where rg='.$tmp_galaxie.' and rp='.$tmp_planet.'';

			$SQL_Result = tic_mysql_query( $sql, $SQL_DBConn );
		 $count =  mysqli_num_rows($SQL_Result);
    if ( $count == 0 ) {
        echo 'Keine Scans vorhanden.';
        return;
    } else {


					// all
        // sektor
        $pts = 0; $me  = 0; $ke  = 0; $sgen=0; $szeit='-'; $s=0; $d=0; $a=0;
        // unit init
        $ja   = 0; $bo   = 0; $fr   = 0; $ze   = 0; $kr   = 0; $sl   = 0; $tr   = 0; $ka   = 0; $ca   = 0; $ugen=0; $uzeit='-';
        // mili init
        $ja0  = 0; $bo0  = 0; $fr0  = 0; $ze0  = 0; $kr0  = 0; $sl0  = 0; $tr0  = 0; $ka0  = 0; $ca0  = 0; $mgen=0; $mzeit='-';
        $ja1  = 0; $bo1  = 0; $fr1  = 0; $ze1  = 0; $kr1  = 0; $sl1  = 0; $tr1  = 0; $ka1  = 0; $ca1  = 0;
        $ja2  = 0; $bo2  = 0; $fr2  = 0; $ze2  = 0; $kr2  = 0; $sl2  = 0; $tr2  = 0; $ka2  = 0; $ca2  = 0;
        // gscan
        $lo = 0; $ro = 0; $mr = 0; $sr = 0; $aj = 0; $ggen=0; $gzeit='-';
        $rscans = '';

        for ( $i=0; $i<$count; $i++ ) {


		if ( $i<($count-1) )
                $rpnext = tic_mysql_result($SQL_Result, $i+1, 'rp' );
            else
                $rpnext = 999;

            $p = tic_mysql_result($SQL_Result, $i, 'p' );
            $g = tic_mysql_result($SQL_Result, $i, 'g' );
            $type = tic_mysql_result($SQL_Result, $i, 'type' );
            $rp = tic_mysql_result($SQL_Result, $i, 'rp' );
            $rg = tic_mysql_result($SQL_Result, $i, 'rg' );
            $rname = gnuser($rg, $rp);
            $rscans .= sprintf( "%d ", $type );
//echo '<br>type='.$type.' - ';
            switch( $type ) {   // scan-type
                case 0: // sektor
                	$sname	= gnuser($g, $p);
                    $szeit  = tic_mysql_result($SQL_Result, $i, 'zeit' );
                    $sgen   = tic_mysql_result($SQL_Result, $i, 'gen' );
                    $pts    = tic_mysql_result($SQL_Result, $i, 'pts' );
                    $me     = tic_mysql_result($SQL_Result, $i, 'me' );
                    $ke     = tic_mysql_result($SQL_Result, $i, 'ke' );
                    $s      = tic_mysql_result($SQL_Result, $i, 's' );
                    $d      = tic_mysql_result($SQL_Result, $i, 'd' );
                    $a      = tic_mysql_result($SQL_Result, $i, 'a' );
                    break;
                case 1: // unit
                	$uname	= gnuser($g, $p);
                    $uzeit  = tic_mysql_result($SQL_Result, $i, 'zeit' );
                    $ugen   = tic_mysql_result($SQL_Result, $i, 'gen' );
                    $ja     = tic_mysql_result($SQL_Result, $i, 'sfj' );
                    $bo     = tic_mysql_result($SQL_Result, $i, 'sfb' );
                    $fr     = tic_mysql_result($SQL_Result, $i, 'sff' );
                    $ze     = tic_mysql_result($SQL_Result, $i, 'sfz' );
                    $kr     = tic_mysql_result($SQL_Result, $i, 'sfkr' );
                    $sl     = tic_mysql_result($SQL_Result, $i, 'sfsa' );
                    $tr     = tic_mysql_result($SQL_Result, $i, 'sft' );
                    $ka     = tic_mysql_result($SQL_Result, $i, 'sfka' );
                    $ca     = tic_mysql_result($SQL_Result, $i, 'sfsu' );
                    break;
                case 2: // mili-scan
                	  $mname	= gnuser($g, $p);
                    $mzeit  = tic_mysql_result($SQL_Result, $i, 'zeit' );
                    $mgen   = tic_mysql_result($SQL_Result, $i, 'gen' );
                    $ja0    = tic_mysql_result($SQL_Result, $i, 'sf0j' );
                    $bo0    = tic_mysql_result($SQL_Result, $i, 'sf0b' );
                    $fr0    = tic_mysql_result($SQL_Result, $i, 'sf0f' );
                    $ze0    = tic_mysql_result($SQL_Result, $i, 'sf0z' );
                    $kr0    = tic_mysql_result($SQL_Result, $i, 'sf0kr' );
                    $sl0    = tic_mysql_result($SQL_Result, $i, 'sf0sa' );
                    $tr0    = tic_mysql_result($SQL_Result, $i, 'sf0t' );
                    $ka0    = tic_mysql_result($SQL_Result, $i, 'sf0ka' );
                    $ca0    = tic_mysql_result($SQL_Result, $i, 'sf0su' );
                    $ja1    = tic_mysql_result($SQL_Result, $i, 'sf1j' );
                    $bo1    = tic_mysql_result($SQL_Result, $i, 'sf1b' );
                    $fr1    = tic_mysql_result($SQL_Result, $i, 'sf1f' );
                    $ze1    = tic_mysql_result($SQL_Result, $i, 'sf1z' );
                    $kr1    = tic_mysql_result($SQL_Result, $i, 'sf1kr' );
                    $sl1    = tic_mysql_result($SQL_Result, $i, 'sf1sa' );
                    $tr1    = tic_mysql_result($SQL_Result, $i, 'sf1t' );
                    $ka1    = tic_mysql_result($SQL_Result, $i, 'sf1ka' );
                    $ca1    = tic_mysql_result($SQL_Result, $i, 'sf1su' );
                    $ja2    = tic_mysql_result($SQL_Result, $i, 'sf2j' );
                    $bo2    = tic_mysql_result($SQL_Result, $i, 'sf2b' );
                    $fr2    = tic_mysql_result($SQL_Result, $i, 'sf2f' );
                    $ze2    = tic_mysql_result($SQL_Result, $i, 'sf2z' );
                    $kr2    = tic_mysql_result($SQL_Result, $i, 'sf2kr' );
                    $sl2    = tic_mysql_result($SQL_Result, $i, 'sf2sa' );
                    $tr2    = tic_mysql_result($SQL_Result, $i, 'sf2t' );
                    $ka2    = tic_mysql_result($SQL_Result, $i, 'sf2ka' );
                    $ca2    = tic_mysql_result($SQL_Result, $i, 'sf2su' );

                    break;
                case 3: // geschtz
                	$gname	= gnuser($g, $p);
                    $gzeit  = tic_mysql_result($SQL_Result, $i, 'zeit' );
                    $ggen   = tic_mysql_result($SQL_Result, $i, 'gen' );
                    $lo     = tic_mysql_result($SQL_Result, $i, 'glo' );
                    $lr     = tic_mysql_result($SQL_Result, $i, 'glr' );
                    $mr     = tic_mysql_result($SQL_Result, $i, 'gmr' );
                    $sr     = tic_mysql_result($SQL_Result, $i, 'gsr' );
                    $aj     = tic_mysql_result($SQL_Result, $i, 'ga' );
                    break;
                default:
                    echo '????huh?!??? - Ohooooh';
                    break;
            }
						// echo '('.$rpnext.' <>'. $rp.')';
        if ( $rpnext <> $rp ) {

            if ($sgen >= "90") { $sgencolor = "01,03"; }
        	  elseif ($sgen >= "50" && $sgen <= "89") { $sgencolor = "01,08"; }
        	  else { $sgencolor = "01,04"; }
        	  if ($ugen >= "90") { $ugencolor = "01,03"; }
        	  elseif ($ugen >= "50" && $ugen <= "89") { $ugencolor = "01,08"; }
        	  else { $ugencolor = "01,04"; }
        	  if ($ggen >= "90") { $ggencolor = "01,03"; }
        	  elseif ($ggen >= "51" && $ggen <= "89") { $ggencolor = "01,08"; }
        	  else { $ggencolor = "01,04"; }
        	  if ($mgen >= "90") { $mgencolor = "01,03"; }
        	  elseif ($mgen >= "50" && $mgen <= "89") { $mgencolor = "01,08"; }
        	  else { $mgencolor = "01,04"; }

						if($_GET['istscanart'] == 'sek') {
                        			$text =       	  "°".'00,10Sektorscan (01,10 '.$sgencolor.' '.$sgen.' %00,10 ) '.$rname.' (01,10'.$rg.':'.$rp.'00,10)';
                        			$text = $text."°".'00,01Punkte: 07,01'.number_format($pts, 0, ',', '.').' 00,01Astros: 07,01'.$a;
                        			$text = $text."°".'00,01Schiffe: 07,01'.$s.' 00,01Geschtze: 07,01'.$d.'';
                        			$text = $text."°".'00,01Metall-Exen: 07,01'.$me.' 00,01Kristall-Exen: 07,01'.$ke.'';
                        			$text = $text."°".'00,01Datum: 07,01'.$szeit.' 00,01gescannt von: 07,01'.$sname.'';
                        			}


						if($_GET['istscanart'] == 'einheit') {

						$text = 	  "°".'00,10Einheitenscan (01,10 '.$ugencolor.' '.$ugen.' %00,10 ) '.$rname.' (01,10'.$rg.':'.$rp.'00,10)';
						$text = $text."°".'00,01Leo: 07,01'.$ja.' 00,01Aquilae: 07,01'.$bo.' 00,01Fronax: 07,01'.$fr.' 00,01Draco: 07,01'.$ze.' 00,01Goron: 07,01'.$kr.'';
						$text = $text."°".'00,01Pentalin: 07,01'.$sl.' 00,01Zenit: 07,01'.$tr.' 00,01Cleptor: 07,01'.$ka.' 00,01Cancri: 07,01'.$ca.'';
						$text = $text."°".'00,01Datum: 07,01'.$uzeit.' 00,01gescannt von: 07,01'.$uname.'';


						}

						if($_GET['istscanart'] == 'gscan') {

						$text = 	  "°".'00,10Geschtzscan (01,10 '.$ggencolor.' '.$ggen.' %00,10 ) '.$rname.' (01,10'.$rg.':'.$rp.'00,10)';
						$text = $text."°".'00,01Rubium: 07,01'.$lo.' 00,01Pulsar: 07,01'.$lr.' 00,01Coon: 07,01'.$mr.'';
						$text = $text."°".'00,01Centurion: 07,01'.$sr.' 00,01Horus: 07,01'.$aj.'';
						$text = $text."°".'00,01Datum: 07,01'.$gzeit.' 00,01gescannt von: 07,01'.$gname.'';


						}

						if($_GET['istscanart'] == 'mili') {

						$text = 	  "°".'00,10Militrscan (01,10 '.$mgencolor.' '.$mgen.' %00,10 ) '.$rname.' (01,10'.$rg.':'.$rp.'00,10)';
						$text = $text."°".'00,1Orbit: 07,01'.$ja0.' 00,1Leo 07,01'.$bo0.' 00,1Aquilae 07,01'.$fr0.' 00,1Fornax 07,01'.$ze0.' 00,1Draco 07,01'.$kr0.' 00,1Goron 07,01'.$sl0.' 00,1Pentalin 07,01'.$tr0.' 00,1Zenit 07,01'.$ka0.' 00,1Cleptor 07,01'.$ca0.' 00,1Cancri ';
						$text = $text."°".'00,01Flotte1: 07,01'.$ja1.' 00,01Leo 07,01'.$bo1.' 00,01Aquilae 07,01'.$fr1.' 00,01Fornax 07,01'.$ze1.' 00,01Draco 07,01'.$kr1.' 00,01Goron 07,01'.$sl1.' 00,01Pentalin 07,01'.$tr1.' 00,01Zenit 07,01'.$ka1.' 00,01Cleptor 07,01'.$ca1.' 00,01Cancri ';
						$text = $text."°".'00,01Flotte2: 07,01'.$ja2.' 00,01Leo 07,01'.$bo2.' 00,01Aquilae 07,01'.$fr2.' 00,01Fornax 07,01'.$ze2.' 00,01Draco 07,01'.$kr2.' 00,01Goron 07,01'.$sl2.' 00,01Pentalin 07,01'.$tr2.' 00,01Zenit 07,01'.$ka2.' 00,01Cleptor 07,01'.$ca2.' 00,01Cancri ';
						$text = $text."°".'00,01Datum: 07,01'.$mzeit.' 00,01gescannt von: 07,01'.$mname.'';
						}




		// all
            // sektor
            $pts = 0; $me  = 0; $ke  = 0; $sgen=0; $szeit='-'; $s=0; $d=0; $a=0;
            // unit init
            $ja   = 0; $bo   = 0; $fr   = 0; $ze   = 0; $kr   = 0; $sl   = 0; $tr   = 0; $ka   = 0; $ca   = 0; $ugen=0; $uzeit='-';
            // mili init
            $ja0  = 0; $bo0  = 0; $fr0  = 0; $ze0  = 0; $kr0  = 0; $sl0  = 0; $tr0  = 0; $ka0  = 0; $ca0  = 0; $mgen=0; $mzeit='-';
            $ja1  = 0; $bo1  = 0; $fr1  = 0; $ze1  = 0; $kr1  = 0; $sl1  = 0; $tr1  = 0; $ka1  = 0; $ca1  = 0;
            $ja2  = 0; $bo2  = 0; $fr2  = 0; $ze2  = 0; $kr2  = 0; $sl2  = 0; $tr2  = 0; $ka2  = 0; $ca2  = 0;
            // gscan
            $lo = 0; $ro = 0; $mr = 0; $sr = 0; $aj = 0; $ggen=0; $gzeit='-';
            $rscans = '';


		}



										}

			}





	}



}

	### Ally Status abfragen
	elseif($_GET['modus']==3) {
   $SQL_Result5 = tic_mysql_query('SELECT id, name, tag, info_bnds, info_naps, info_inoffizielle_naps, info_kriege, code FROM `gn4allianzen` where ticid="'.$Benutzer['ticid'].'" ;', $SQL_DBConn);

   $SQL_Num5=mysqli_num_rows($SQL_Result5);

   for($x='0';$x<$SQL_Num5;$x++){
	 $id=tic_mysql_result($SQL_Result5,$x,'id');
	 $name=tic_mysql_result($SQL_Result5,$x,'name');
	 $tag=tic_mysql_result($SQL_Result5,$x,'tag');
	 $bnds=tic_mysql_result($SQL_Result5,$x,'info_bnds');
	 $naps=tic_mysql_result($SQL_Result5,$x,'info_naps');
	 $defcon=tic_mysql_result($SQL_Result5,$x,'code');
	 $krieg=tic_mysql_result($SQL_Result5,$x,'info_kriege');
     if (!isset($id)) $id = 0;
     if (!isset($name)) $name = 0;
     if (!isset($tag)) $tag = 0;
     if ($bnds=='') $bnds = 0;
     if ($naps=='') $naps = 0;
     if ($defcon=='') $defcon = 0;
     if ($krieg=='') $krieg = 0;
	 $text=$text.$id."|".$name."|".$tag."|".$bnds."|".$naps."|".$defcon."|".$krieg."°";
   }
 }
    ###Tic Statistiken
   	elseif($_GET['modus']==4) {
           		$SQL_Result1 = tic_mysql_query('SELECT COUNT(*) FROM `gn4flottenbewegungen` where ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row1 = mysqli_fetch_row($SQL_Result1);
                $SQL_Result2 = tic_mysql_query('SELECT COUNT(*) FROM `gn4flottenbewegungen` where modus=1 and ticid="'.$Benutzer['ticid'].'"' , $SQL_DBConn);
                $SQL_Row2 = mysqli_fetch_row($SQL_Result2);
                $SQL_Result3 = tic_mysql_query('SELECT COUNT(*) FROM `gn4flottenbewegungen` where modus=2and ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row3 = mysqli_fetch_row($SQL_Result3);
                $SQL_Result4 = tic_mysql_query('SELECT COUNT(*) FROM `gn4flottenbewegungen` where modus>2 and ticid="'.$Benutzer['ticid'].'" or modus=0 and ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row4 = mysqli_fetch_row($SQL_Result4);
                $SQL_Result5 = tic_mysql_query('SELECT COUNT(*) FROM `gn4accounts` where ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row5 = mysqli_fetch_row($SQL_Result5);
                $SQL_Result8 = tic_mysql_query('SELECT COUNT(*) FROM `gn4forum` WHERE belongsto="0" and ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row8 = mysqli_fetch_row($SQL_Result8);
                $SQL_Result9 = tic_mysql_query('SELECT COUNT(*) FROM `gn4forum` WHERE NOT belongsto="0" and ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row9 = mysqli_fetch_row($SQL_Result9);
                $SQL_Result10 = tic_mysql_query('SELECT COUNT(*) FROM `gn4scans` where ticid="'.$Benutzer['ticid'].'"', $SQL_DBConn);
                $SQL_Row10 = mysqli_fetch_row($SQL_Result10);
                $text= "00,01Anzahl Flottenbewegungen: 07,01".ZahlZuText($SQL_Row1[0])."°00,01Anzahl Verteidingungsflge: 07,01".ZahlZuText($SQL_Row2[0])."°00,01Anzahl Angriffsflge: 07,01".ZahlZuText($SQL_Row3[0])."°00,01Anzahl Rckflge: 07,01".ZahlZuText($SQL_Row4[0])."°"."00,01Anzahl der T.I.C. Accounts: 07,01".ZahlZuText($SQL_Row5[0])."°"."00,01Forenstatistik: 07,01"."°"."00,01Themen: 07,01".ZahlZuText($SQL_Row8[0])."°"."00,01Antworten: 07,01".ZahlZuText($SQL_Row9[0])."°"."00,01Scan Datenbank: 07,01"."°"."00,01Anzahl Scans: 07,01".ZahlZuText($SQL_Row10[0])."°"."00,01Letzte Scansuberung: 07,01".$lastscanclean;







   	}
    ### Top5 Scaner
   	elseif($_GET['modus']==5) {
	 		$SQL_Result11 = tic_mysql_query('SELECT * FROM `gn4accounts` WHERE scantyp = 1 and ticid="'.$Benutzer['ticid'].'" ORDER BY svs DESC;', $SQL_DBConn);
			$text=$text."°".$irc_text['farbe'].$irc_farbe['orange']."MILI-SCANNER";
			for ($n = 0; $n < 5; $n++) {
					$name  = tic_mysql_result($SQL_Result11, $n, 'name' );
					$svs = tic_mysql_result($SQL_Result11, $n, 'svs' );
					$gala = tic_mysql_result($SQL_Result11, $n, 'galaxie' );
					$planet = tic_mysql_result($SQL_Result11, $n, 'planet' );
					$text=$text."°".$name." ( ".$gala.":".$planet." ) hat ".$svs." svs";
			}
			$SQL_Result12 = tic_mysql_query('SELECT * FROM `gn4accounts` WHERE scantyp = 2 and ticid="'.$Benutzer['ticid'].'" ORDER BY svs DESC;', $SQL_DBConn);
						$text=$text."°".$irc_text['farbe'].$irc_farbe['orange']."NEWS-SCANNER";
						for ($m = 0; $m < 5; $m++) {
								$name  = tic_mysql_result($SQL_Result12, $m, 'name' );
								$svs = tic_mysql_result($SQL_Result12, $m, 'svs' );
								$gala = tic_mysql_result($SQL_Result12, $m, 'galaxie' );
								$planet = tic_mysql_result($SQL_Result12, $m, 'planet' );


						        $text=$text."°".$name." ( ".$gala.":".$planet." ) hat ".$svs." svs";
			}


	}elseif($_GET['modus']==6) {
$text=$_GET['text'];
$text=str_replace(",","",$text);
$text=str_replace(".","",$text);
$text=str_replace("%","",$text);
$scan = explode (" ", $text);
$nr = count($scan);
  if($scan['1']=='Sektorscan'){
  $modi=0;
  $qry=$scan['5'].', '.$scan['6'].', '.$scan['7'].', '.$scan['8'].', '.$scan['9'];
  $formart='pts, s, d, me, ke';
  }
  if($scan['1']=='Einheitenscan'){
  $modi=1;
  $qry=$scan['5'].', '.$scan['6'].', '.$scan['7'].', '.$scan['8'].', '.$scan['9'].', '.$scan['10'].', '.$scan['11'].', '.$scan['12'].', '.$scan['13'];
  $formart = 'sfka, sfsu, sff, sfz, sfkr, sfsa, sft, sfj, sfb';
  }
  if($scan['1']=='Militärscan'){
  $modi=2;
  $qry=$scan['5'].', '.$scan['6'].', '.$scan['7'].', '.$scan['8'].', '.$scan['9'].', '.$scan['10'].', '.$scan['11'].', '.$scan['12'].', '.$scan['13'].', '.$scan['14'];
  $qry=$qry.', '.$scan['15'].', '.$scan['16'].', '.$scan['17'].', '.$scan['18'].', '.$scan['19'].', '.$scan['20'].', '.$scan['21'].', '.$scan['22'].', '.$scan['25'].', '.$scan['26'];
  $qry=$qry.', '.$scan['27'].', '.$scan['28'].', '.$scan['29'].', '.$scan['30'].', '.$scan['31'].', '.$scan['32'].', '.$scan['33'];
    $formart = 'sf0ka, sf0su, sf0f, sf0z, sf0kr, sf0sa, sf0t, sf0j, sf0b';
    $formart = $formart.', sf1ka, sf1su, sf1f, sf1z, sf1kr, sf1sa, sf1t, sf1j, sf1b';
    $formart = $formart.', sf2ka, sf2su, sf2f, sf2z, sf2kr, sf2sa, sf2t, sf2j, sf2b';
  }
  if($scan['1']=='Geschützscan'){
  $modi=3;
  $qry=$scan['5'].', '.$scan['6'].', '.$scan['7'].', '.$scan['8'].', '.$scan['9'];
  $formart = 'glo, glr, gmr, gsr, ga';
  }
 if(isset($modi)){
 $koords=explode(":",$scan['4']);
 $koord= explode(":",$_GET['koord']);
 tic_mysql_query('DELETE FROM `gn4scans` WHERE rg="'.$koords['0'].'" AND rp="'.$koords['1'].'" AND type="'.$modi.'";', $SQL_DBConn) or die(mysqli_errno()." - ".mysqli_error());
 tic_mysql_query('INSERT INTO `gn4scans` (p, g, type, zeit, rg, rp, gen, '.$formart.') VALUES ("'.$koord['0'].'", "'.$koord['1'].'", "'.$modi.'", "'.date("H:i d.m.Y").'", "'.$koords['0'].'", "'.$koords['1'].'", "'.$scan['2'].'", '.$qry.');', $SQL_DBConn) or die(mysqli_errno()." - ".mysqli_error());
addgnuser($koords['0'], $koords['1'], $scan['3']);
}
}elseif($_GET['modus']==7) {
if(isset($_GET['auth'])){
$SQL_Result=tic_mysql_query('SELECT * FROM `gn4accounts` WHERE authnick="'.$_GET['auth'].'";', $SQL_DBConn) or die(mysqli_errno()." - ".mysqli_error());
$ok= 'ok';
if(mysqli_num_rows($SQL_Result)!=1){
$ok = 'fail '.mysqli_num_rows($SQL_Result);
$ticid  = '0';
$ally = '0';
$status = '0';
$rang = '0';
}else{
$ticid  = tic_mysql_result($SQL_Result, 0, 'ticid' );
$ally = tic_mysql_result($SQL_Result, 0, 'allianz' );
$status = tic_mysql_result($SQL_Result, 0, 'spy' );
$rang = tic_mysql_result($SQL_Result, 0, 'rang' );
}
echo '|'.$ok.'|'.$status.'|'.$ticid.'|'.$ally.'|'.$rang;
}
}



	if ($_GET['modus']==4)
	{
		$text = $irc_text['fett'].$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelblau'].'[T.I.C - Statistik]'.$irc_text['farbe'].$irc_text['fett']."°".$text;
		$text = $text."°".$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelgrau'].'[ http://'.$HTTP_HOST.'     coding by http://www.tic-entwickler.de]'.$irc_text['farbe'];
	}
	elseif ($_GET['modus']==5)
		{
			$text = $irc_text['fett'].$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelblau'].'[T.I.C - Scanner]'.$irc_text['farbe'].$irc_text['fett'].$text;
			$text = $text."°".$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelgrau'].'[ http://'.$HTTP_HOST.'     coding by http://www.tic-entwickler.de]'.$irc_text['farbe'];
	}
    elseif ($_GET['modus']!=3 && $_GET['modus']!=4 && $_GET['modus']!=5 && $_GET['modus']!=6 && $_GET['modus']!=7)
    {
    	$text = $irc_text['fett'].$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelblau'].'[ T.I.C. | Tactical Information Center ]'.$irc_text['farbe'].$irc_text['fett']."".$text;
		$text = $text."°".$irc_text['farbe'].$irc_farbe['weiss'].','.$irc_farbe['dunkelgrau'].'[ http://'.$HTTP_HOST.'     coding by http://www.tic-entwickler.de]'.$irc_text['farbe'];
    }
    echo $text;
?>
