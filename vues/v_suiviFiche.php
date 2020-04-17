﻿<div id="contenu">
	<h3>Fiche de frais du mois <?php echo $dateMois?> : 
		</h3>
		<div class="encadre">
		<p>
			Etat : <b><?php echo $libEtat?></b> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>
				  
						 
		</p>
		<table class="listeLegere">
		   <caption>Eléments forfaitisés </caption>
			<tr>
			 <?php
			 foreach ( $lesFraisForfait as $unFraisForfait ) 
			 {
				$libelle = $unFraisForfait['libelle'];
			?>	
				<th style="font-size:1vw;"> <?php echo $libelle?></th>
			 <?php
			}
			?>
			</tr>
			<tr>
			<?php
			  foreach (  $lesFraisForfait as $unFraisForfait  ) 
			  {
					$quantite = $unFraisForfait['quantite'];
			?>
					<td class="qteForfait"><?php echo $quantite?> </td>
			 <?php
			  }
			?>
			</tr>
		</table>
		<table class="listeLegere">
		   <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
		   </caption>
				 <tr>
					<th class="date">Date</th>
					<th class="libelle">Libellé</th>
					<th class='montant'>Montant</th>                
				 </tr>
			<?php      
			  foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
			  {
				$date = $unFraisHorsForfait['date'];
				$libelle = $unFraisHorsForfait['libelle'];
				$montant = $unFraisHorsForfait['montant'];
			?>
				 <tr>
					<td><?php echo $date ?></td>
					<td><?php echo $libelle ?></td>
					<td><?php echo $montant ?></td>
				 </tr>
			<?php 
			  }
			?>
		</table>
		<br/>
		<?php
			if($libEtat == "Validée et mise en paiement"){
		?>
		<form action="index.php?uc=suivrePaiement&action=ficheRemboursee" method="post">
			<input type="submit" class='buttonSimpleHF' name="ok" value="Valider le remboursement de la fiche"/>
		</form>
		<?php
			}
		?>
	  </div>
	  </div>
	 













