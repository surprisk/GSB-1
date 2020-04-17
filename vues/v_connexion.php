<div id="contenu">
      <h2>Identification utilisateur</h2>


<form method="POST" action="index.php?uc=connexion&action=valideConnexion">
   
    
	<p>
       <label for="nom">Login*</label>
       <input id="login" class='formSimpleHF' type="text" name="login"  size="30" maxlength="45">
    </p>
	<p>
		<label for="mdp">Mot de passe*</label>
		<input id="mdp" class='formSimpleHF'  type="password"  name="mdp" size="30" maxlength="45">
    </p>
        <input type="submit" class='buttonSimpleHF' value="Valider" name="valider">
        <input type="reset" class='buttonSimpleHF' value="Annuler" name="annuler"> 
</form>

</div>