<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsbV2';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
		private static $monPdo;
		private static $monPdoGsb=null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}

/**
 * Retourne si un utilisateur est un visiteur ou non
 
 * @param $idUser
 * @return le booléen de l'affirmation
*/
	public function estVisiteur($id){
		$bool = false;
		$req = PdoGsb::$monPdo->query("select utilisateur.idTypeUser from utilisateur 
		where utilisateur.id='$id'");
		$row = $req->fetch();
		if($row['idTypeUser'] == 2){
			$bool = true;
		}
		return $bool;
	}
	
/**
 * Retourne les informations d'un visiteur
 
 * @param $login
 * @param $mdp
 * @return les informations de l'utilisateur sous la forme d'un tableau associatif 
*/
	
	public function getInfosUtilisateur($login, $mdp){
		$req = PdoGsb::$monPdo->query("select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom, utilisateur.idTypeUser from utilisateur 
		where utilisateur.login='$login' and utilisateur.mdp='$mdp'");
		$row = $req->fetch();
		return $row;
	}

/**
 * Retourne toutes les informations d'un visiteur
 
 * @param $id 
 * @return toutes les informations de l'utilisateur sous la forme d'un tableau associatif 
*/
	public function getToutesInfosUtilisateur($id){
		$req = PdoGsb::$monPdo->query("select* from utilisateur where utilisateur.id='$id'");
		$row = $req->fetch();
		return $row;
	}
	
/**
 * Retourne l'identité d'un visiteur
 
 * @param $id 
 * @return le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getIdentitéUtilisateur($id){
		$req = "select utilisateur.nom as nom, utilisateur.prenom as prenom from utilisateur 
		where utilisateur.id='$id'";
		$rs = PdoGsb::$monPdo->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}
	
/**
 * Retourne un tableau contenant le nom des visiteurs ayant une fiche à valider
 * @return le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getLesVisiteursAvecFiche(){
		$req = "select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom from utilisateur, fichefrais where utilisateur.id = fichefrais.idVisiteur and idEtat = 'CL' and idTypeUser = 2";
		$visiteurs = array();
		$rs = PdoGsb::$monPdo->query($req);
		while($donnees = $rs->fetch()){
			$visiteurs[] = $donnees;
		}
		return $visiteurs;
	}
	
/**
 * Retourne un tableau contenant le nom des visiteurs ayant une fiche validée ou remboursée
 * @return le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getLesVisiteursAvecFicheValidee(){
		$req = "select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom from utilisateur, fichefrais where utilisateur.id = fichefrais.idVisiteur and (idEtat = 'VA' or idEtat = 'RB') and idTypeUser = 2 group by utilisateur.id, utilisateur.nom, utilisateur.prenom";
		$visiteurs = array();
		$rs = PdoGsb::$monPdo->query($req);
		while($donnees = $rs->fetch()){
			$visiteurs[] = $donnees;
		}
		return $visiteurs;
	}

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concernées par les deux arguments
 
 * La boucle foreach ne peut être utilisée ici car on procède
 * à une modification de la structure itérée - transformation du champ date-
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur' 
		and lignefraishorsforfait.mois = '$mois' ";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs 
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concernées par les deux arguments
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite, fraisforfait.montant 
		from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
	
/**
 * Retourne tous les id de la table FraisForfait
 
 * @return un tableau associatif 
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Met à jour la table ligneFraisForfait
 
 * Met à jour la table ligneFraisForfait pour un visiteur et
 * un mois donné en enregistrant les nouveaux montants
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
 * @return un tableau associatif 
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			PdoGsb::$monPdo->exec($req);
		}
	}
/**
 * met à jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concerné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux 
*/	
	public function estPremierFraisMois($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur' and fichefrais.idEtat = 'CR'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}
/**
 * Retourne le dernier mois en cours d'un visiteur
 
 * @param $idVisiteur 
 * @return le mois sous la forme aaaamm
*/	
	public function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}
	
/**
 * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
 
 * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
 * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function creeNouvellesLignesFrais($idVisiteur,$mois){
		$dernierMois = $this->dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
		if($laDerniereFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais($idVisiteur, $dernierMois,'CL');
				
		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
		PdoGsb::$monPdo->exec($req);
		$lesIdFrais = $this->getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
			PdoGsb::$monPdo->exec($req);
		 }
	}
/**
 * Crée un nouveau frais hors forfait pour un visiteur un mois donné
 * à partir des informations fournies en paramètre
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format français jj//mm/aaaa
 * @param $montant : le montant
*/
	public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
		$dateFr = dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
		values(null,'$idVisiteur','$mois','$libelle','$dateFr','$montant')";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Supprime le frais hors forfait dont l'id est passé en argument
 
 * @param $idFrais 
*/
	public function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
		PdoGsb::$monPdo->exec($req);
	}
	
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' 
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
	
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais cloturee
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisAValiderDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and idEtat = 'CL' 
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
	
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais validee
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisValidesDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and (idEtat = 'VA' or idEtat = 'RB')
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
	
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais à valider
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisAValider($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and idEtat='CL'
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
	
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
*/	
	public function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs, 
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}
/**
 * Modifie l'état et la date de modification d'une fiche de frais
 
 * Modifie le champ idEtat et met la date de modif à aujourd'hui
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update ficheFrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}
	
/**
 * Modifie automatiquement l'etat de toutes les fiches frais CR en CL
 * Ne prend pas de paramètre
 */
	
	public function majEtatFicheFraisAll(){
		$req = "update ficheFrais set idEtat='CL'
		where idEtat='CR';";
		PdoGsb::$monPdo->exec($req);
	}

/**
 * Permet de savoir si la fiche est cloturéeou non
 * @return $bool
 */
	public function ficheCloturee($mois){
		$req1 = "select * from  fichefrais where mois = '$mois'";
		$req2 = PdoGsb::$monPdo->query($req1);
		$res = $req2->fetch();
		$bool = false; 
		if($res['idEtat']=='CL' or $res['idEtat']=='VA' or $res['idEtat']=='RB'){
			$bool = true;
		} 
	return $bool;
	}
	
/**
 * Permet de crypter un mot de passe saisi pour comparer à la connexion
 * @return $npasswd
 */
	public function passwdCrypt($passwd){
		$npasswd = hash ("sha512" , "$passwd");
		return $npasswd;
	}
	
/**
 * Permet de récupérer l'état d'une fiche en fonction d'un mois et d'un id d'utilisateur
 * @return $laLigne['libelle'] : libelle d'un état
 */
	public function getEtatFiche($mois,$id){
		$req = "select libelle from fichefrais inner join etat on ficheFrais.idEtat = Etat.id
			where idvisiteur ='$id' and mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['libelle'];
	}
	
	public function getNbLigne($mois, $id){
		$req1 = "select count(idVisiteur) AS totalFF FROM lignefraisforfait WHERE idVisiteur='$id' AND mois='$mois'";
		$req2 = "select count(idVisiteur) AS totalHF FROM lignefraishorsforfait WHERE idVisiteur='$id' AND mois='$mois'";
		$res1 = PdoGsb::$monPdo->query($req1);
		$res2 = PdoGsb::$monPdo->query($req2);
		$ret1 = $res1->fetch();
		$ret2 = $res2->fetch();
		return $ret1['totalFF']+$ret2['totalHF'];	
	}
	
/**
 *
 *
 */
	public function majDateModif($mois, $idVisiteur){
		$date = date("Y-m-d");
		$req = "update fichefrais set dateModif = '$date' where mois = '$mois' and idVisiteur = '$idVisiteur'";
		PdoGsb::$monPdo->exec($req);
	}
	
/**
 *
 *
 */
	public function reporterMoisSuivant($mois,$idVisiteur, $idLigne){
		if(substr($mois,4,6) == 12){
			$nmois = substr($mois,0,4).'01';
		}
		else{
			$nmois = substr($mois,0,4).substr( $mois,4,6)+1;
		}
		$req = "update lignefraishorsforfait set mois = '$nmois' where mois = '$mois' and idVisiteur = '$idVisiteur' and id = $idLigne";
		PdoGsb::$monPdo->exec($req);
	}
	
/**
 *
 *
 */
	public function miseEnPaiement($mois,$idVisiteur){
		if(substr($mois,4,6) == 12){
			$nmois = substr($mois,0,4).'01';
		}
		else{
			$nmois = substr($mois,0,4).substr( $mois,4,6)+1;
		}
		$req = "update fichefrais set idEtat = 'VA' where mois = '$mois' and idVisiteur = '$idVisiteur'";
		PdoGsb::$monPdo->exec($req);
		$this->upMontantValide($idVisiteur, $mois);
	}
	
/**
 *
 *
 */
	public function etatRemboursé($mois,$idVisiteur){
		if(substr($mois,4,6) == 12){
			$nmois = substr($mois,0,4).'01';
		}
		else{
			$nmois = substr($mois,0,4).substr( $mois,4,6)+1;
		}
		$req = "update fichefrais set idEtat = 'RB' where mois = '$mois' and idVisiteur = '$idVisiteur'";
		PdoGsb::$monPdo->exec($req);
	}

/**
 *
 *
 */
	public function refuserFraisHF($id, $mois,$idVisiteur){
		$req = "select libelle FROM lignefraishorsforfait where id='$id' and idVisiteur='$idVisiteur' and mois='$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$ret = $res->fetch();
		$req = "update lignefraishorsforfait set libelle='[REFUSE]".$ret['libelle']."' WHERE id=$id and idVisiteur='$idVisiteur' and mois='$mois'";
		$res = PdoGsb::$monPdo->exec($req);
	}

/**
 *	Permet de calculer le montant total pour chaque ligne de frais forfait grâce à un idVisiteur et un mois
 *	@return $array : array() indexé des totaux
 */
	public function totalLigneFF($idVisiteur, $mois){
		$req = "select * from lignefraisforfait L, fraisforfait F where L.idFraisForfait = F.id AND idVisiteur = '$idVisiteur' and mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$ret = $res->fetchAll();
		$i=0;
		$array = array();
		foreach($ret as $l){
			$array[$i] = $l['quantite']*$l['montant'];
			$i++;
		}
		
		return $array;
	}

/**
 *	Permet de calculer le montant total pour chaque ligne de frais hors forfait grâce à un idVisiteur et un mois
 *	@return $total : float du total
 */
	public function totalLigneHF($idVisiteur, $mois){
		$req = "select * from lignefraishorsforfait where idVisiteur = '$idVisiteur' and mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$ret = $res->fetchAll();
		$total=0;
		foreach($ret as $l){
			if(substr($l['libelle'], 0, 8)!= "[REFUSE]"){
				$total+=$l['montant'];
			}
		}
		return $total;
	}
	
/**
 *	Permet de calculer le montant validé total en fonction d'un idVisiteur et d'un mois
 *	@return $total : float du total
 */
	public function upMontantValide($idVisiteur, $mois){
		$tff=$this->totalLigneFF($idVisiteur, $mois);
		$thf=$this->totalLigneHF($idVisiteur, $mois);
		$total=0;
		foreach($tff as $l){
			$total+=$l;
		}
		$total+=$thf;
		
		$req="UPDATE fichefrais SET montantValide = $total WHERE idVisiteur = '$idVisiteur' and mois = '$mois'";
		$res = PdoGsb::$monPdo->exec($req);
	}
	
	public function getMontantValideAll($mois){
		$req = "select * from utilisateur where idTypeUser=2";
		$res = PdoGsb::$monPdo->query($req);
		$ret = $res->fetchAll();
		$total=0;
		
		foreach($ret as $u){
			$tff=$this->totalLigneFF($ret['id'], $mois);
			$thf=$this->totalLigneHF($ret['id'], $mois);
			foreach($tff as $l){
				$total+=$l;
			}
			$total+=$thf;
		}
		
		return $total;
	}
	
	public function getNbLigneAll($mois){
		$req = "select * from utilisateur where idTypeUser=2";
		$res = PdoGsb::$monPdo->query($req);
		$ret = $res->fetchAll();
		$total=0;
		
		foreach($ret as $u){
			$nbLigne=$this->getNbLigne($mois, $ret['id']);
			$total+=$nbLigne;
		}
		
		return $total;
	}
	public function passwdCryptAll(){
	$req = PdoGsb::$monPdo -> query('SELECT * FROM utilisateur');
	while($donnees = $req->fetch()){
		$passwd = $donnees['mdp'];
		$npasswd = hash("sha512" , "$passwd");
		PdoGsb::$monPdo -> query("UPDATE utilisateur SET mdp='$npasswd' WHERE id='".$donnees['id']."'");
	}
}
}
?>