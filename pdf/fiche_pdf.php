<?php
	session_start();
	require_once("../include/fct.inc.php");
	require_once ("../include/class.pdogsb.inc.php");
	$pdo = PdoGsb::getPdoGsb();
	$estConnecte = estConnecte();
	
	if(!$estConnecte){
		header("Location:../index.php");
	} 

	$mois = $_POST['mois'];
	$idVisiteur = $_SESSION['idVisiteur'];
	$etatFiche = $pdo->getEtatFiche($mois,$idVisiteur);
	$dateMois = substr( $mois,4,6).'/'.substr( $mois,0,4);
	
	$totalHT = 0;
	$totalTVA = 0;
	
	//Appel de la librairie FPDF
	require("../fpdf/fpdf.php");
	
	// Création de la class PDF
	class PDF extends FPDF {
		// Header
		function Header() {
			// Logo
			//Source de l'image, position a gauche, position en haut, largeur d'image
			$this->Image('../images/logo.jpg',8,2,50);
			// Saut de ligne
			$this->Ln(20);
		}
		// Footer
		function Footer(){
			// Positionnement à 1,5 cm du bas
			$this->SetY(-15);
			// Arial italique 8
			$this->SetFont('Arial','I',8);
			// Couleur du texte en gris
			$this->SetTextColor(128);
			// Numéro de page
			//largeur ligne, hauteur ligne, texte, bordure, retour a la ligne, centrer
			$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
		}
		
	}

	$euro = '€';
	$euro = strtr($euro, array('€' => chr(128)));
	
	// Activation de la classe
	ob_get_clean();
	$pdf = new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Helvetica','',11);

	$pdf->SetFillColor(15,109,205);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->SetY(20);
	$pdf->SetX(70);
	$pdf->Cell(120,10,'FICHE DE REMBOURSEMENT',0,0,'R',1);
	
	//on remet la bonne couleur de texte
	$pdf->SetTextColor(0);
	
	$row = $pdo->getToutesInfosUtilisateur($idVisiteur);
	
	// Infos de la commande calées à gauche
	//position gauche, haut, texte
	$pdf->Text(18,50,utf8_decode('Fiche du mois : '.$dateMois));
	$pdf->Text(18,58,utf8_decode('Etat fiche : '.$etatFiche));

	// Infos du client calées à droite
	$pdf->Text(145,50,utf8_decode($row['nom']).' '.utf8_decode($row['prenom']));
	$pdf->Text(145,55,utf8_decode($row['adresse']));
	$pdf->Text(145,60,$row['cp'].' '.utf8_decode($row['ville']));
	
	// Position de l'entête à 10mm des infos (70 + 10)
	$position_entete = 80;

	function entete_table($position_entete){
		global $pdf;
		$pdf->SetDrawColor(183); // Couleur du fond
		$pdf->SetFillColor(221); // Couleur des filets
		$pdf->SetTextColor(0); // Couleur du texte
		$pdf->SetY($position_entete);
		//position de la première colonne 
		$pdf->SetX(10);
		$pdf->Cell(88,8,'Frais Forfaitaires',1,0,'L',1);
		$pdf->SetX(98);
		$pdf->Cell(25,8,utf8_decode('Quantité'),1,0,'L',1);
		$pdf->SetX(123); 
		$pdf->Cell(40,8,'Montant unitaire',1,0,'C',1);
		$pdf->SetX(163);
		$pdf->Cell(30,8,'Total',1,0,'C',1);
	}
	
	entete_table($position_entete);
	
	// Liste des détails
	$position_detail = 88; // Position à 8mm de l'entête

	$head = false;
	
	$lesFraisForfait= $pdo->getLesFraisForfait($idVisiteur,$mois);
	foreach ($lesFraisForfait as $valeur) {
		foreach($valeur as $cle2 =>$valeur2){
			if($cle2 == 'libelle' and $cle2 <> 'idFrais'){
				$position_entete = $position_entete+8;
				$pdf->SetY($position_entete);
				$pdf->Cell(88,8,utf8_decode($valeur2),1,'C');
			}
			if($cle2 == 'quantite' && $cle2 <> '0'){
				$quantite = $valeur2;
				$pdf->SetY($position_detail);
				$pdf->SetX(98);
				$pdf->MultiCell(25,8,$valeur2,1,'C');
			}
			if($cle2 == 'montant' && $cle2 <> '0'){
				$montant = $valeur2;
				$pdf->SetY($position_detail);
				$pdf->SetX(123);
				$pdf->MultiCell(40,8,$valeur2.$euro,1,'C');
				//positionner la ligne suivante 8mm plus bas
				$position_detail += 8;
			}
			if(isset($montant) and isset($quantite)){
				$pdf->SetY($position_detail-8);
				$pdf->SetX(163);
				$pdf->MultiCell(30,8,$quantite*$montant.$euro,1,'C');
				unset($montant);
				unset($quantite);
			}
		}
	}
	
	$position_detail = $position_detail+8;
	
	$pdf->Text(85,$position_detail,"Frais hors forfait");
	$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur,$mois);
	
	$position_detail = $position_detail+5;
	if($head == false){
		//tableau frais hors forfait
		$pdf->setY($position_detail);
		$pdf->SetX(10);
		$pdf->Cell(51,8,'Date',1,0,'L',1);
		$pdf->setY($position_detail);
		$pdf->SetX(61);
		$pdf->Cell(81,8,'Libelle',1,0,'L',1);
		$pdf->setY($position_detail);
		$pdf->SetX(142);
		$pdf->Cell(51,8,'Montant',1,0,'L',1);
		$head = true;
	}
	//positionner la ligne suivante 8mm plus bas
	$position_detail += 8;
	foreach ($lesFraisHorsForfait as $valeur) {
		foreach($valeur as $cle2 =>$valeur2){
			if($cle2 == 'date' and $cle2 <> 'idFrais'){
				$pdf->SetY($position_detail);
				$pdf->SetX(10);
				$pdf->Cell(51,8,utf8_decode($valeur2),1,'C');
			}
			if($cle2 == 'libelle' && $cle2 <> '0'){
				$pdf->SetY($position_detail);
				$pdf->SetX(61);
				$pdf->MultiCell(81,8,$valeur2,1,'C');
			}
			if($cle2 == 'montant' && $cle2 <> '0'){
				$pdf->SetY($position_detail);
				$pdf->SetX(142);
				$pdf->MultiCell(51,8,$valeur2.$euro,1,'C');
				//positionner la ligne suivante 8mm plus bas
				$position_detail += 8;
			}
		}
	}
	/*
	$pdf->setY($position_max+8);
	$pdf->SetX(20);
	$pdf->Cell(25,8,$row2['prixArticle']*$row2['qteCommandee'].$euro,1,0,'L',0);
	$totalHT = $totalHT + $row2['prixArticle']*$row2['qteCommandee'];
	$pdf->setY($position_max+8);
	$pdf->SetX(45);
	$pdf->Cell(25,8,'20,00%',1,0,'L',0);
	$pdf->setY($position_max+8);
	$pdf->SetX(70);
	$pdf->Cell(25,8,($row2['prixArticle']*$row2['qteCommandee']*0.2).$euro,1,0,'L',0);
	$totalTVA = $totalTVA + $row2['prixArticle']*$row2['qteCommandee']*0.2;
	$position_ligne += 8;
	
	//tableau prix total
	$pdf->setY($position_ligne + 8);
	$pdf->SetX(120);
	$pdf->Cell(25,8,'Total HT',1,0,'L',1);
	$pdf->setY($position_ligne + 8);
	$pdf->SetX(145);
	$pdf->Cell(25,8,$totalHT.$euro,1,0,'L',0);
	$pdf->setY($position_ligne + 16);
	$pdf->SetX(120);
	$pdf->Cell(25,8,'Total TVA',1,0,'L',1);
	$pdf->setY($position_ligne + 16);
	$pdf->SetX(145);
	$pdf->Cell(25,8,$totalTVA.$euro,1,0,'L',0);
	$pdf->setY($position_ligne + 24);
	$pdf->SetX(120);
	$pdf->Cell(25,8,'Total TTC',1,0,'L',1);
	$pdf->setY($position_ligne + 24);
	$pdf->SetX(145);
	$pdf->Cell(25,8,($totalHT+$totalTVA).$euro,1,0,'L',0);
	*/
	
	// Nom du fichier
	$nom = 'Fiche de remboursement-'.$row['nom'].' '.$row['prenom'].'-'.$dateMois.'.pdf';

	// Création du PDF
	$pdf->Output($nom,'I');
?>