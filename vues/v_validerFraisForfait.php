<div id="contenu">
<h2>Valider la fiche de frais de <?php echo $visiteur['nom']." ".$visiteur['prenom']; ?> du mois <?php echo $dateMois ?></h2>


<div class="corpsForm">

<fieldset>
	<legend>Eléments forfaitisés
	</legend>
	<?php
	foreach ($lesFraisForfait as $unFrais){
		$idFrais = $unFrais['idfrais'];
		$libelle = $unFrais['libelle'];
		$quantite = $unFrais['quantite'];
	?>
	<p>
		<?php echo $libelle." ".$quantite?>
	</p>

	<?php
		}
	?>
	</div>
	<div class="piedForm">
		<p>
			<form method="POST"  action="index.php?uc=validerFrais&action=validerFraisForfait">
				<input  class='buttonSimpleHF' id="ok" type="submit" value="Valider les frais" size="20" />
			</form>
			<form method="POST"  action="index.php?uc=validerFrais&action=modifierFraisForfait">
				<input type="hidden" name="mois" value="<?php echo $dateMois; ?>"/>
				<input  class='buttonSimpleHF' id="modif" type="submit" value="Modifier les frais" size="20" />
			</form>
		</p> 
	</div>

</fieldset>
