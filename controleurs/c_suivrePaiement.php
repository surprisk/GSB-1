<?php
if($_SESSION['idTypeUser'] == 1){
	include("vues/v_sommaire_comptable.php");
}
else{
	include("vues/v_sommaire_visiteur.php");
}
$action = $_REQUEST['action'];
switch($action){
	case 'selectionnerVisiteur':{
		$lesVisiteurs = $pdo->getLesVisiteursAvecFicheValidee();
		include("vues/v_saisieVisiteur.php");
		break;
	}
	case 'selectionnerMois':{
		$_SESSION['idVisiteur'] = $_POST['lstVisiteur'];
		$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
		$lesMois = $pdo->getLesMoisValidesDisponibles($_SESSION['idVisiteur']);
		include("vues/v_saisieMois.php");
		break;
	}
	case 'suivrePaiement':{
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
		
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'],$mois);
		$lesFraisForfait= $pdo->getLesFraisForfait($_SESSION['idVisiteur'],$mois);
		$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteur'],$mois);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		
		include("vues/v_suiviFiche.php");
		
		break;
	}
	case 'ficheRemboursee':{
		$mois = $_SESSION['mois'];
		$mois = substr( $mois,3,6).substr( $mois,0,2);
		$pdo->etatRemboursé($mois, $_SESSION['idVisiteur']);
		header("Location:index.php?uc=suivrePaiement&action=suivrePaiement");
		break;
	}
}
?>