<table class="listeLegere">
	<caption>Descriptif des éléments hors forfait
	</caption>
	<tr>
		<th class="date">Date</th>
		<th class="libelle">Libellé</th>  
		<th class="montant">Montant</th>  
		<th class="action">Refuser</th>
		<th class="action">Reporter</th>
	</tr>

	<?php    
		foreach( $lesFraisHorsForfait as $unFraisHorsForfait) {
			$libelle = $unFraisHorsForfait['libelle'];
			$date = $unFraisHorsForfait['date'];
			$montant=$unFraisHorsForfait['montant'];
			$id = $unFraisHorsForfait['id'];
	?>		
	<tr>
		<td> <?php echo $date ?></td>
		<td><?php echo $libelle ?></td>
		<td><?php echo $montant ?></td>
		<td><a href="index.php?uc=validerFrais&action=supprimerFrais&idFrais=<?php echo $id ?>" 
		onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');"><div class="buttonHF" style="background-color: red;"></div></a></td>
		<td><a href="index.php?uc=validerFrais&action=reporterFrais&idFrais=<?php echo $id ?>" 
		onclick="return confirm('Voulez-vous vraiment reporter ce frais?');"><div class="buttonHF" style="background-color: yellow;"></div></a></td>
	</tr>
	<?php
		}
	?>
</table>

<div id='doneHF'>

<div class='buttonCase'>
<center>
	<form action="index.php?uc=validerFrais&action=majNbJustificatifs" method="post">
		<p>Nombre de justificatifs :
		<input class='formSimpleHF' type="number" min="0" name="nbJustitficatifs" value="<?php echo $nowJustif ?>"/>
		<input class='buttonSimpleHF' type="submit" value="Valider" size="30"/>
	</form>
</center>
</div>

<center>
	<form action="index.php?uc=validerFrais&action=miseEnPaiement" method="post">
		<input class='buttonVal' type="submit" value="Valider et mettre en paiement la fiche" size="60"/>
		<input type="hidden" name="lstVisiteur" value="<?php echo $_SESSION['idVisiteur'] ?>"/>
	</form>
</center>

</div>

<center>
	<form action="index.php?uc=validerFrais&action=selectionnerMois" method="post">
		<input class='buttonSimpleHF' style='margin-top:15px;' type="submit" value="Retour" size="30"/>
		<input type="hidden" name="lstVisiteur" value="<?php echo $_SESSION['idVisiteur'] ?>"/>
</center>