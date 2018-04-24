<?
global $aMsgText;
$aMsgText = array();

// ========== errors, msg: 1___ ==============
$aMsgText[1000] = ' nekorekti ievades dati';	//incorrect input data 
$aMsgText[1010] = ' lauks ir obligāts';		//field is mandatory 
$aMsgText[1100] = ' laukam jāsastāv no skaitļiem';	//field must be numeric 
$aMsgText[1110] = ' lauks ir par mazu';		//field is too small 
$aMsgText[1120] = ' lauks ir par lielu';		//field is too big 
$aMsgText[1200] = 'fatāla bāzes kļūda';		//fatal base error 
$aMsgText[1300] = 'datu bāzei liegta pieeja'; //data base access denied 
$aMsgText[1400] = 'Faila izmērs ir par lielu!';		//File is too big 
// s_print_template
$aMsgText[1410] = 'Error uploading HTML file!';		
$aMsgText[1411] = 'Error uploading CSS file!';		
$aMsgText[1420] = 'Template name must be unique!';		

$aMsgText[1500] = 'Nekorekts datums'; // Not valid date 
$aMsgText[1510] = 'Nekorekts datuma atdalījums'; // Not valid date separator
$aMsgText[1520] = 'Nekorekts datuma formāts (dd.mm.YYYY)!'; // Not valid date format!

$aMsgText[1600] = 'Nevar izdzēst.';	//Can not delete

// ========== OK: 2___ ==============
$aMsgText[2000] = 'Dati ir saglabāti';	//  Data have been saved



// ============ login, logout, permissions - 3___ ==============
//title
$aMsgText[3000] = 'Kļūdaina autorizācija.'; //Incorrect authorization
$aMsgText[3010] = 'Darba sesijas kļūda.'; //Session error
$aMsgText[3012] = 'Kļūda sesijas datos.'; 
$aMsgText[3014] = 'Sesijas, IP kļūda.'; 
$aMsgText[3020] = 'Auditācijas pierakstu kļūda.'; //Security auditing error
$aMsgText[3030] = 'Neatbilstoši pārlūkprogrammas uzstādījumi.'; //Your browser has incorrect security settings
$aMsgText[3040] = 'Logout';
$aMsgText[3050] = 'Nav atļauta piekļuve.'; //Access not permited
$aMsgText[3052] = 'Nav tiesību uz doto moduli.'; 
//text
$aMsgText[3100] = 'Nepareizs lietotāja vārds vai parole, vai lietotājs ir bloķēts'; 
					//Incorrect username, parsword or your login account is blocked
$aMsgText[3110] = 'Pieprasītais modulis neeksistē sistēmā vai nav pieejams ar jūsu lietotāja tiesībām. Jūs esat atvienoti no sistēmas drošības nolūkos. '; //Requested module does not exists or you dont have permission to access it
$aMsgText[3120] = 'Sistēmai neizdevās veikt auditācijas pierakstus. Jūs esat atvienoti no sistēmas drošības nolūkos.';
					//Security auditing system could not recgister your data. Session closed
$aMsgText[3130] = 'Lai lietotu sistēmu ir nepieciešams atļaut POP-UP logu atvēršanu pārlūkprogrammas uzstādījumos.';
					//To work with application you have to disable pop-up blocking
$aMsgText[3140] = 'Sesija pārtraukta'; //Session closed
$aMsgText[3150] = 'No jūsu IP adresi nav atļauts piekļūt sistēmai'; //Your network IP address is blocked
// s_office_perm
$aMsgText[3160] = 'Your office do not have access to this!';


//button, system
$aMsgText[3300] = 'Turpināt'; //Continue
$aMsgText[3310] = 'iepriekšējā lappuse'; 
$aMsgText[3311] = 'nākamā lappuse'; 
$aMsgText[3350] = 'Lietotājs'; 
$aMsgText[3360] = 'Laiks'; 

// paroles, lietotājs
// s_user_edit.php, s_password_edi.php
$aMsgText[3400] = 'Paroles nesakrīt!';
$aMsgText[3401] = 'Jaunievadītās paroles nesakrīt!';
$aMsgText[3410] = 'Nepareiza parole!';
$aMsgText[3411] = 'Parole nedrīkst būt tukša!';
$aMsgText[3430] = 'Tāds lietotājvārds jau ir!';
$aMsgText[3440] = 'Parole nomainīta veiksmīgi!';

// halt_error
$aMsgText[3500] = 'Sistēmas Kļūda'; 
$aMsgText[3510] = 'Nosūtiet šo kļūdu uz'; 



// ============ special - 4___ ==============
// together: 40__
$aMsgText[4000] = 'Name must be given';

// syscanfig: 42__
$aMsgText[4200] = 'Key must be given'; 
$aMsgText[4201] = 'Such key already is for '; 
$aMsgText[4210] = 'Cannot delete. Item has sub-item.'; 
$aMsgText[4211] = 'Cannot delete. Item is referenced elsewhere.'; 

// s_valid_ip.php 
$aMsgText[4300] = 'Beigu IP adresei jābūt lielākai par sākuma'; 
// s_help.php 
$aMsgText[4310] = 'Must be browse image'; 

?>