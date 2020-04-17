<div id="contenu">
	<h2>Fiches de frais de : </h2>
	<?php if($_REQUEST['uc'] == 'suivrePaiement'){ ?>
		<form action="index.php?uc=suivrePaiement&action=selectionnerMois" method="post">
	<?php 
		}
		else if($_REQUEST['uc'] == 'validerFrais'){ 
	?>
		<form action="index.php?uc=validerFrais&action=selectionnerMois" method="post">
	<?php } ?>
		<h3>Visiteur à sélectionner : </h3>
		<div class="corpsForm">
		
			<?php
				if(count($lesVisiteurs) == 0 and $_REQUEST['uc'] == 'suivrePaiement'){
					?>
					<p>Il n'y a pas de visiteurs ayant une fiche validée</p>
					<?php
				}
				else if(count($lesVisiteurs) == 0 and $_REQUEST['uc'] == 'validerFrais'){
					?>
					<p>Il n'y a pas de visiteurs ayant une fiche à valider</p>
					<?php
				}
				else{
			?>

			<p>

			<label for="lstVisiteur" accesskey="n">Visiteur : </label>
			<select id="lstVisiteur" class='formSimpleHF' name="lstVisiteur">
				<?php
					foreach ($lesVisiteurs as $unVisiteur) {
						$nom = $unVisiteur['nom'];
						$prenom =  $unVisiteur['prenom'];
						$id = $unVisiteur['id'];
						?>
						<option value="<?php echo $id ?>"><?php echo $nom." ".$prenom ?></option>
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
</div>