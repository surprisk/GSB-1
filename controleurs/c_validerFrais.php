<?php
if($_SESSION['idTypeUser'] == 1){
	include("vues/v_sommaire_comptable.php");
}
else{
	include("vues/v_sommaire_visiteur.php");
}
$action = $_REQUEST['action'];
switch($action){
	case 'confirmerSaison':{
		include("vues/v_debutSaison.php");
		break;
	}
	case 'selectionnerVisiteur':{
		$pdo->majEtatFicheFraisAll();
		$lesVisiteurs = $pdo->getLesVisiteursAvecFiche();
		include("vues/v_saisieVisiteur.php");
		break;
	}
	case 'selectionnerMois':{
		$_SESSION['idVisiteur'] = $_POST['lstVisiteur'];
		$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
		$lesMois = $pdo->getLesMoisAValiderDisponibles($_SESSION['idVisiteur']);
		include("vues/v_saisieMois.php");
		break;
	}
	case 'validerFiche':{
		if(isset($_SESSION['erreur'])){
			ajouterErreur($_SESSION['erreur']);
			include("vues/v_erreurs.php");
			unset($_SESSION['erreur']);
		}
		if(isset($_POST['lstMois'])){
			$_SESSION['mois'] = $_POST['lstMois'];
		}
		if(isset($_SESSION['mois'])){
			$dateMois = $_SESSION['mois'];
		}
		$mois = substr( $dateMois,3,6).substr( $dateMois,0,2);
		$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'],$mois);
		$lesFraisForfait= $pdo->getLesFraisForfait($_SESSION['idVisiteur'],$mois);
		$nowJustif = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $mois);
		include("vues/v_validerFraisForfait.php");
		include("vues/v_validerFraisHorsForfait.php");
		break;
	}
	case 'modifierFraisForfait':{
		$dateMois = $_POST['mois'];
		$mois = substr( $dateMois,3,6).substr( $dateMois,0,2);
		$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
		$lesFraisForfait= $pdo->getLesFraisForfait($_SESSION['idVisiteur'],$mois);
		include("vues/v_modifierFraisForfait.php");
		break;
	}
	case 'validerModifFraisForfait':{
		$lesFrais = $_REQUEST['lesFrais'];
		$mois = $_POST['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
		//si les valeurs sont valides
		if(lesQteFraisValides($lesFrais)){
	  	 	echo $pdo->majFraisForfait($_SESSION['idVisiteur'],$mois,$lesFrais);
		}
		//si les valeurs sont invalides : erreur et réaffichage de la page
		else{
			ajouterErreur("Les valeurs des frais doivent être numériques");
			include("vues/v_erreurs.php");
			$dateMois = $_POST['mois'];
			$mois = substr( $dateMois,3,6).substr( $dateMois,0,2);
			$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
			$lesFraisForfait= $pdo->getLesFraisForfait($_SESSION['idVisiteur'],$mois);
			include("vues/v_modifierFraisForfait.php");
		}
		header("Location:index.php?uc=validerFrais&action=validerFiche");
		break;
	}
	case 'reporterFrais':{
		$idFrais = $_GET['idFrais'];
		$mois = $_SESSION['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
		$pdo->reporterMoisSuivant($mois,$_SESSION['idVisiteur'], $idFrais);
		$pdo->majDateModif($mois, $_SESSION['idVisiteur']);
		header("Location:index.php?uc=validerFrais&action=validerFiche");
		break;
	}
	case 'majNbJustificatifs':{
		$mois = $_SESSION['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
		$pdo->majNbJustificatifs($_SESSION['idVisiteur'], $mois, $_POST['nbJustitficatifs']);
		$pdo->majDateModif($mois, $_SESSION['idVisiteur']);
		header("Location:index.php?uc=validerFrais&action=validerFiche");
		break;
	}
	case 'miseEnPaiement':{
		$mois = $_SESSION['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
		$minJustif = $pdo->getNbLigne($mois, $_SESSION['idVisiteur']);
		$nowJustif = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $mois);
		if($nowJustif < $minJustif){
			$_SESSION['erreur'] = "La fiche ne dispose pas d'un nombre suffisant de justificatifs pour être validée et mise en paiement";
			header("Location:index.php?uc=validerFrais&action=validerFiche");
		}
		else{
			$mois = $_SESSION['mois'];
			$mois = substr( $mois,3,6).substr( $mois,0,2);
			$pdo->miseEnPaiement($mois,$_SESSION['idVisiteur']);
			$pdo->majDateModif($mois, $_SESSION['idVisiteur']);
			header("Location:index.php?uc=validerFrais&action=selectionnerVisiteur");
		}
		break;
	}
	case 'supprimerFrais':{
		$idFrais = $_REQUEST['idFrais'];
		$mois = $_SESSION['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
	    $pdo->refuserFraisHF($idFrais, $mois, $_SESSION['idVisiteur']);
		header("Location:index.php?uc=validerFrais&action=validerFiche");
		break;
	}
}
?>