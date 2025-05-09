🧠 Fonctionnalités côté back à développer :
🔐 Authentification & gestion des utilisateurs => Fait
Inscription, connexion (Symfony Security) => Fait

Gestion de rôles (admin vs user, par exemple pour la modération d’avis)

🎮 Gestion des jeux (via API RAWG + base locale)
Appel API pour récupérer les infos (titres, genres, studios…)

Enregistrement local d’un jeu “vu” ou “tracké” (évite de ping l’API en boucle)

Cache ou synchronisation partielle dans la BDD (ex : sauvegarder titre, image, rating, etc.)

📚 Système de backlog
Ajouter un jeu à une de ces listes perso : "À faire", "En cours", "Terminé", "Abandonné"

Modifier le statut (changer de liste)

CRUD sur ses listes de jeux

🗓️ Sessions de jeu (optionnel mais intéressant)
L’utilisateur peut créer une session (ex : “joué 2h le 22 avril”)

Calcul du temps total joué (et affichage dans le profil)

✍️ Avis et notes personnalisées
L’utilisateur peut noter un jeu + ajouter une critique

Affichage sur la page du jeu (notes agrégées)

Modération (par admin ou auto-modération)

🧾 Statistiques personnelles
% de jeux terminés

Temps total passé à jouer (si sessions activées)

Genre préféré, plateforme la plus utilisée, etc.
