<div id="contenu">
<h2>Modifier la fiche de frais de <?php echo $visiteur['nom']." ".$visiteur['prenom']; ?> du mois <?php echo $dateMois ?></h2>


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
	<form method="POST"  action="index.php?uc=validerFrais&action=validerModifFraisForfait">
	<p style="text-align:right;margin-right:35%;">
		<?php echo $libelle ?>
		<input type="text" class='formSimpleHF' id="idFrais" name="lesFrais[<?php echo $idFrais?>]" size="10" maxlength="5" value="<?php echo $quantite?>" >
	</p>

	<?php
		}
	?>
	</div>
	<div class="piedForm">
		<p>
			<input type="hidden" name="mois" value="<?php echo $dateMois; ?>"/>
			<input id="ok" type="submit" class='buttonSimpleHF' value="Valider modifications" size="20" />
			</form>
			<form method="POST"  action="index.php?uc=validerFrais&action=validerFiche">
				<input type="hidden" name="lstMois" value="<?php echo $dateMois; ?>"/>
				<input id="annuler" type="submit" class='buttonSimpleHF' value="Annuler" size="20" />
			</form>
		</p> 
	</div>

</fieldset>
