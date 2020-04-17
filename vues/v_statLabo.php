 <div id="contenu">
<?php
	for($i = 0; $i < 12; $i++){
		$mois = $i+1;
		if(strlen($mois) == 1){
			$mois = '0'.$mois;
		}
		?>
		<p>
			Mois : <?php echo $mois.'/'.$annee; ?>
			<br/>Montant total remboursé par le labo : <?php echo "x" ;?>
			<br/>Nombre de prestations prises en compte: <?php echo "x" ; ?>
		</p>
		<?php
	}
?>
</div>