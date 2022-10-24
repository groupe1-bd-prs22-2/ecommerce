# Accès à l'application

---

URL de l'application 
-----------

Vous pouvez accéder à l'application via l'URL suivante : https://mangamania.herokuapp.com/

Compte administrateur
-----------

Pour accéder à l'administration de l'application, vous pouvez utiliser les identifiants suivants :
- Utilisateur : **administrateur@mangamania.com**
- Mot de passe : **Adm1nTest** 

Carte de paiement Stripe
-----------

Pour réaliser un achat, le numéro de carte Stripe à utiliser est le suivant : _**4242 4242 4242 4242**_.
La date d'expiration et le CVC sont libres de saisie tant qu'ils sont valides.

Accès à l'interface Stripe
-----------

Vous pouvez également accéder à l'interface Stripe (https://dashboard.stripe.com/) afin de récupérer/visualiser les paiements effectués en utilisant les identifiants suivants :
- Utilisateur : **t.rajoarifera@ecole-ipssi.net**
- Mot de passe : **30jt%3S!8!kj**

Pour consulter les paiements réalisés, vous pouvez vous rendre à cette URL une fois authentifiée : https://dashboard.stripe.com/test/payments

Accès aux e-mails
-----------

Pour visualiser les mails envoyés par l'application, il faudra se connecter à MailTrap (https://mailtrap.io/) en utilisant les identifiants suivants :
- Utilisateur : **manga.mania.ipssi@gmail.com**
- Mot de passe : **SkasLR0Q436x1G4GHlq9**

---

# Prise en main de l'image Docker #

---

Build de l'image
-----------

1. Lancer le build de l'image docker via la commande suivante : `USER_ID=$(id -u) GROUP_ID=$(id -g) USERNAME=$(whoami) docker compose build`
2. Une fois le build complété, vous pouvez démarrer l'image en exécutant : `docker compose up -d`

**NOTE** : La commande de build est très importante, si cette dernière est mal exécutée, vous risquez de rencontrer des
problèmes de permission sur les fichiers générés (par les commandes _Symfony_ par exemple), voire de ne pas pouvoir lancer
certaines commandes.

Lancement des différentes commandes
-----------

Pour lancer toute commande _Symfony_ / _Composer_ / _Yarn_ / _Npm_, utiliser la commande `docker compose run --rm` suivie du container à
utiliser ainsi que la commande à exécuter.

Par exemple, pour lancer les migrations Symfony, il faudra donc exécuter la commande suivante :
`docker compose run --rm php symfony console d:m:m`

Les différents containers utilisables pour lancer les commandes sont :
- `php` : Permet de lancer toutes les commandes Symfony / Composer
- `node` : Permet de lancer toutes les commandes Yarn / Npm
- `database` : Permet d'accéder / interagir avec la base de données

Ces derniers sont indiqués dans le fichier `docker-compose.yml` à la racine du projet, listés dans `services`.

**Mode terminal interactif**

Afin d'éviter d'avoir à toujours saisir `docker compose run --rm $CONTAINER $CMD`, vous pouvez également lancer un
terminal directement dans le container voulu en utilisant les commandes suivantes selon le service désiré :
- `php` : `docker compose run --rm php bash`
- `node` : `docker compose run --rm node sh`

