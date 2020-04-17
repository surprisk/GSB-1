<?php
require_once("include/fct.inc.php");
require_once ("include/class.pdogsb.inc.php");
include("vues/v_entete.php") ;
session_start();
$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();
//vérifier l'état de connexion de l'utilisateur
if(!isset($_REQUEST['uc']) || !$estConnecte){
     $_REQUEST['uc'] = 'connexion';
}	 
$uc = $_REQUEST['uc'];
//appeler le bon controleur selon 
switch($uc){
	case 'connexion':{ 
		include("controleurs/c_connexion.php");
		break;
	}
	case 'gererFrais' :{
		if($pdo->estVisiteur($_SESSION['idUtilisateur'])){
			include("controleurs/c_gererFrais.php");
		}
		else{
			header("Location:index.php?uc=connexion&action=sommaire");
		}
		break;
	}
	case 'etatFrais' :{
		if($pdo->estVisiteur($_SESSION['idUtilisateur'])){
			include("controleurs/c_etatFrais.php");
		}
		else{
			header("Location:index.php?uc=connexion&action=sommaire");
		}
		break; 
	}case 'statLabo' :{
		if($pdo->estVisiteur($_SESSION['idUtilisateur'])){
			include("controleurs/c_statLabo.php");
		}
		else{
			header("Location:index.php?uc=connexion&action=sommaire");
		}
		break; 
	}
	case 'validerFrais':{ 
		include("controleurs/c_validerFrais.php");
		break;
	}
	case 'suivrePaiement':{ 
		if(!$pdo->estVisiteur($_SESSION['idUtilisateur'])){
			include("controleurs/c_suivrePaiement.php");
		}
		else{
			header("Location:index.php?uc=connexion&action=sommaire");
		}
		break;
	}
}
include("vues/v_pied.php") ;
?>

