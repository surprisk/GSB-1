<div id="contenu">
      <h2>Renseigner ma fiche de frais du mois <?php echo $numMois."-".$numAnnee ?></h2>
        
		<?php
			if($pdo->FicheCloturee($mois)){
		?>
		<p>La fiche est cloturée, vous pourrez saisir votre fiche le 1er jour du prochain mois</p>
		<a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais"><input type="submit" class='buttonSimpleHF' value="Consulter mes fiches de frais"></a>
		<?php 
			}
			else{
		?>
      <form method="POST"  action="index.php?uc=gererFrais&action=validerMajFraisForfait">
      <div class="corpsForm">
          
          <fieldset>
            <legend>Eléments forfaitisés
            </legend>
			<?php
					foreach ($lesFraisForfait as $unFrais)
					{
						$idFrais = $unFrais['idfrais'];
						$libelle = $unFrais['libelle'];
						$quantite = $unFrais['quantite'];
			?>
				<p style="text-align:right;margin-right:35%;">
						<?php echo $libelle ?>
					<input type="text" class='formSimpleHF' id="idFrais" name="lesFrais[<?php echo $idFrais?>]" size="10" maxlength="5" value="<?php echo $quantite?>" >
				</p>
			
			<?php
					}
			?>
			
			
			
			
           
          </fieldset>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" class='buttonSimpleHF' value="Valider" size="20" />
        <input id="annuler" type="reset" class='buttonSimpleHF' value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>
		<?php
			}
		?>
  