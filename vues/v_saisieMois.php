<div id="contenu">
	<h2>Fiches de frais de <?php echo $visiteur['nom']." ".$visiteur['prenom']; ?> : </h2>
	<?php if($_REQUEST['uc'] == 'suivrePaiement'){ ?>
		<form action="index.php?uc=suivrePaiement&action=suivrePaiement" method="post">
	<?php 
		}
		else if($_REQUEST['uc'] == 'validerFrais'){ 
	?>
		<form action="index.php?uc=validerFrais&action=validerFiche" method="post">
	<?php } ?>
		
		<h3>Mois à sélectionner : </h3>
		<div class="corpsForm">
			<?php
				if(count($lesMois) == 0 and $_REQUEST['uc'] == 'suivrePaiement'){
					?>
					<p>Il n'y a pas de fiches validées pour cet utilisateur</p>
					<?php
				}
				else if(count($lesMois) == 0 and $_REQUEST['uc'] == 'validerFrais'){
					?>
					<p>Il n'y a pas de fiches à valider pour cet utilisateur</p>
					<?php
				}
				else{
			?>

			<p>

			<label for="lstMois" accesskey="n">Mois : </label>
			<select id="lstMois" class='formSimpleHF' name="lstMois">
			<?php
				foreach ($lesMois as $unMois) {
					$mois = $unMois['mois'];
					$numAnnee =  $unMois['numAnnee'];
					$numMois =  $unMois['numMois'];
					?>
						<option value="<?php echo $numMois."/".$numAnnee ?>"><?php echo  $numMois."/".$numAnnee ?> </option>
					<?php 

				}
			?> 

			</select>
			</p>
		</div>
		<div class="piedForm">
			<p>
			<input id="ok" type="submit" class='buttonSimpleHF' value="Valider" size="20" />
			</p> 
		</div>
		<?php
		
			}
			
		?>

	</form>
	<?php if($_REQUEST['uc'] == 'suivrePaiement'){ ?>
		<center><form action="index.php?uc=suivrePaiement&action=selectionnerVisiteur" method="post"><input type="submit" class='buttonSimpleHF' value="Retour" size="30"/></form></center>
	<?php 
		}
		else if($_REQUEST['uc'] == 'validerFrais'){ 
	?>
		<center><form action="index.php?uc=validerFrais&action=selectionnerVisiteur" method="post"><input type="submit" class='buttonSimpleHF' value="Retour" size="30"/></form></center>
	<?php } ?>
</div>