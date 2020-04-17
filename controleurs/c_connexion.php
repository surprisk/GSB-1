<?php
if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action){
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
		$login = $_REQUEST['login'];
		$mdp = $_REQUEST['mdp'];
		$mdp = $pdo->passwdCrypt($mdp);
		$utilisateur = $pdo->getInfosUtilisateur($login,$mdp);
		if(empty($login) || empty ($mdp)){
			ajouterErreur("Veuillez remplir tous les champs");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else{
			if(!is_array( $utilisateur)){
				ajouterErreur("Login ou mot de passe incorrect");
				include("vues/v_erreurs.php");
				include("vues/v_connexion.php");
			}
			else{
				$_SESSION['idUtilisateur'] = $utilisateur['id'];
				$id = $utilisateur['id'];
				$nom =  $utilisateur['nom'];
				$prenom = $utilisateur['prenom'];
				$idTypeUser = $utilisateur['idTypeUser'];
				connecter($id,$nom,$prenom,$idTypeUser);
				if($idTypeUser == 1){
					include("vues/v_sommaire_comptable.php");
				}
				else{
					include("vues/v_sommaire_visiteur.php");
				}
			}
		}
		break;
	}
	default :{
		//Réinitialisation du tableau de session
		//on le vide intégralement
		$_SESSION = array();
		//destruction de la session
		session_destroy();
		//destruction du tableau de session
		session_unset();
		include("vues/v_connexion.php");
		break;
	}
}
?>