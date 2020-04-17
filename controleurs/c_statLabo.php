<?php
if($_SESSION['idTypeUser'] == 1){
	include("vues/v_sommaire_comptable.php");
}
else{
	include("vues/v_sommaire_visiteur.php");
}
//CODE NON CORRECT CI DESSOUS
//PENSER A POUVOIR SAISIR SEULEMENT LES 12 DERNIERS MOIS ET CEUX AVEC UN MONTANT REMBOURSE SEULEMENT

$annee = getMois(date("d/m/Y"));
$annee = substr( $annee,0,4);
include("vues/v_statLabo.php");
/*
$visiteur = $pdo->getIdentitéUtilisateur($_SESSION['idVisiteur']);
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteur'],$mois);
$lesFraisForfait= $pdo->getLesFraisForfait($_SESSION['idVisiteur'],$mois);
$nowJustif = $pdo->getNbjustificatifs($_SESSION['idVisiteur'], $mois);
include("vues/v_validerFraisForfait.php");
include("vues/v_validerFraisHorsForfait.php");*/
?>