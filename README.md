# exam
BarakaFood est une application web qui connecte les restaurants ayant des surplus alimentaires avec les personnes dans le besoin. L'objectif est de réduire le gaspillage alimentaire tout en aidant la communauté.
Ce projet implémente plusieurs mesures de sécurité :
- Protection SQL Injection : Utilisation de PDO Prepared Statements
- Protection XSS : Échappement HTML avec htmlspecialchars()
- Mots de passe : Hachage avec Bcrypt (cost 12)
- Rate Limiting : Protection contre les attaques DDoS
- Validation des uploads : Vérification MIME type et taille des fichiers
  ⚠️ Note Importante
> config.php est inclus dans ce repository.
> 
> En situation réelle, ce fichier contenant les identifiants de la base de données ne devrait JAMAIS être versionné dans Git. 
> Nous l'avons inclus ici uniquement car c'est un projet d'examen et pour faciliter l'évaluation par le professeur.
