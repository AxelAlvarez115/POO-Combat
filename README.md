# TP Combat POO

## Résumé des compétences visées

- Vous allez utiliser les connaissances acquises sur PDO pour gérer une base de données de héros de jeu.
- Vous allez créer des classes PHP qui ont des rôles bien déterminés et séparés.
- Vous allez en apprendre un peu plus sur l’intérêt des classes.
- Vous allez découvrir le principe de l’auto loading.
- Vous allez découvrir le principe de l’hydratation et l’imbrication de la PDO avec les classes.
- Vous allez utiliser les principes fondamentaux de la POO : l’encapsulation, l’héritage, le polymorphisme.

## Instructions générales

Nous allons découper ce TP en 2 sprints, Une correction sera faite entre les 2.

Pour réaliser ce TP, vous <ins>pouvez</ins> travailler par équipe de deux.

Vous devrez soigner l'apparence de l'application ainsi que l'arborescence du projet.

Vous devrez élaborer une base de données en lien avec les différentes fonctionnalités.

**Au fur et à mesure de la progression, de nouvelles itérations du projet s'ajouteront, vous devez préparer votre code à la maintenabilité et la scalabilité (mise à l'échelle) en découpant vos fonctionnalités le plus possible dans vos objets.**

Un pas à pas est disponible pour vous aider à démarrer le projet. Ce qui vous donnera ensuite les bases pour le deuxième sprint.


## 🏆 Objectifs sprint 1

L'objectif de ce TP est de créer un jeu de combat en tour par tour entre des héros que l'on crée et des monstres automatiquement générés. Voici le déroulé attendu :

1. **Création d'un Héros :** 🦸
   - Chaque visiteur peut créer un héros en saisissant uniquement un nom, ce qui a pour effet de créer un héros en base de données.

   <img src="./assets/AccueilCreateHero.png" alt="accueil-create-hero"/>

2. **Choix du Héros :** 🤔
   - L'utilisateur peut également choisir l'un des héros déjà créés avec lequel se battre, à condition que le héros soit toujours en vie (HP > 0).

   <img src="./assets/HeroChoice.png" alt="hero-choice"/>

3. **Lancement d'un Combat :** ⚔️ 
   - L'utilisateur peut lancer un nouveau combat en cliquant sur le bouton "Choisir un Héros".

4. **Automatisation du Combat :** 🤖
   - Après que le combat a été lancé, un monstre est automatiquement créé. Le déroulé du combat est enregistré dans un tableau PHP utilisé pour afficher les échanges de dégâts entre le monstre et le héros.

   <img src="./assets/Fight.png" alt="hero-choice"/>

5. **Résultat du Combat :** 🏆
   - L'utilisateur voit son héros survivre ou mourir au combat.
   - Lorsqu'un joueur ou le monstre n'a plus de points de vie, le combat est terminé.

6. **Rejouer :** 🔁
   - Donner la possibilité de rejouer. Si le héros survit, il devient sélectionnable à nouveau dans l'accueil.

## Structure du projet

Le projet doit être organisé avec les fichiers suivants en PHP :

1. **config/autoload.php :** 🔄
   - Permet le chargement automatique des classes et configure PHP pour avoir de meilleurs messages d’erreurs.

2. **config/db.php :** 🗃️
   - Gère la connexion à la base de données avec une instance de PDO, similaire à la correction du QCM.

3. **classes/Hero.php :** 🦸
   - Définit la classe `Hero` avec :
     - 2 propriétés :
       - Son nom (unique).
       - Ses points de vie.
     - 1 méthode :
       - `frapper un monstre`.

4. **classes/Monster.php :** 🦹
   - Définit la classe `Monster` avec :
     - 2 propriétés :
       - Son nom.
       - Ses points de vie.

5. **classes/HeroesManager.php :** 🧙‍♂️
   - Définit la classe `HeroesManager` qui contient tout le CRUD d’un héros :
     - Enregistrer un nouveau héros en base de données.
     - Modifier un héros.
     - Sélectionner un héros.
     - Récupérer une liste de plusieurs héros vivants.
     - Savoir si un héros existe.

6. **classes/FightsManager.php :** 🥋
   - Définit la classe `FightsManager` qui stocke les données du combat et comporte ces fonctionnalités :
     - Créer automatiquement un monstre.
     - Déclencher un combat et obtenir les résultats du combat.

7. **index.php :** 🎮
   - Affiche l'interface du mini-jeu de combat.
     - Le joueur peut créer un héros.
     - Le joueur peut sélectionner un héros existant.

8. **fight.php :** ⚔️
   - Utilise les classes instanciées et les méthodes souhaitées sur les objets.
     - Une instance de `HeroesManager` doit être créée.


## Pas à pas Sprint 1

### Préparation de la base de données

- Dans un premier temps, préparez votre base de données. Créez une table `heroes` avec les colonnes suivantes :
  - `id` (en auto-increment),
  - `name` (non nullable),
  - `health_point` (de type int avec une valeur par défaut à 100).

### Préparation des classes et de la connexion à la base de données

1. **config/autoload.php :** 🔄
   - Copiez le code ci-dessous dans le fichier `config/autoload.php` pour avoir de meilleurs messages d’erreurs et charger automatiquement toutes vos classes :
     ```php
     <?php
     // Strict
     declare(strict_types=1);

     // Enable all PHP errors
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);

     // Pretty errors
     ini_set("html_errors", "1");
     ini_set("error_prepend_string", "<pre style='color: #333; font-face:monospace; white-space: pre-wrap;font-size: 17px;color:#880808'>");
     ini_set("error_append_string ", "</pre>");

     // Autoload logic
     function chargerClasse($classname)
     {
         require __DIR__ . '/../classes/' . $classname . '.php';
     }
     spl_autoload_register('chargerClasse');

     // Session
     session_start();
     ```

### Hero.php et HeroesManager.php

#### Classe Hero

- Créez la classe `Hero` avec ses propriétés, son constructeur, ses getters et setters. Préparez également la méthode `hit()` en la laissant vide.

#### Classe HeroesManager

- Créez la classe `HeroesManager` qui a, pour le moment, une propriété privée `$db`. Dans le constructeur, instanciez PDO dans la propriété privée `$db`.

### index.php

- Maintenant que les bases sont prêtes, commencez le traitement. Créez un formulaire dans `index.php` sans action pour que le traitement de création de héros reste sur cette page. Utilisez un seul input : "name".
- Dans une condition, si des données `POST['name']` existent, créez une instance de `HeroesManager($db)` en lui fournissant la connexion à la base de données. Ensuite, appelez la méthode `add()` du manager en lui donnant comme argument une nouvelle instance de la classe `Hero`.

### HeroesManager.php

- Dans la classe `HeroesManager`, créez la première fonction du CRUD en la nommant simplement `add()`. Cette méthode prendra comme argument un objet `Hero`.
- La méthode `add` doit d'abord préparer et exécuter une requête INSERT avec le `name` récupéré dans l'objet `Hero`. `$hero->getName();`
- À ce moment-là du code, un nouveau Hero existe en base de données. Utilisez une méthode native de PDO pour récupérer le dernier id enregistré dans la base de données : `$id = $this->bd->lastInsertId();`,<br>`$hero->setId($id);`
- Indiquez également à l'objet `Hero` ses points de vie.
- Maintenant que vous pouvez insérer des héros en BDD, il faut les afficher. Créez la méthode `findAllAlive()` qui contient une requête PDO SELECT et qui récupère tous les héros qui ont encore leurs points de vie (`pv > 0`). Cette méthode doit ensuite stocker dans un tableau des nouvelles instances de la classe `Hero` pour chaque ligne récupérée dans la base de données. À la fin, la méthode `return` le tableau.

### index.php

- Depuis l’index, appelez maintenant la méthode du manager qui récupère tous les héros vivants et bouclez dessus pour afficher une simple card affichant le nom du héros et ses points de vie. Un bouton "choisir" vous permet d'aller sur la prochaine page de traitement : `fight.php`. Ce bouton est donc un formulaire avec la méthode POST qui contient un input hidden avec l'ID du héros.
- **À ce stade de votre code, il doit être possible dans votre rendu d'enregistrer des héros et de les afficher via la page `index.php`. Si des erreurs persistent, vous devriez être capable de les déboguer.**

### fight.php et HeroesManager.php

- Comme pour `index.php`, dans un premier temps, appelez l'autoloader, la connexion à la base de données, et instanciez un `HeroesManager`.
- Utilisez ensuite une nouvelle fonction du CRUD qui a pour responsabilité de faire une requête PDO SELECT WHERE. Appelez cette méthode `find()` dans `HeroesManager`, elle prendra en argument un entier que vous récupérerez avec `$_POST['hero_id']` (en fonction du nom de votre input hidden).
- La méthode `find` crée ensuite une nouvelle instance de `Hero` avec les informations récupérées et le **retourne**.

### FightManager.php et Monster.php

- Créez le fichier `FightManager.php` et créez la classe correspondante. Faites de même avec le fichier `Monster.php`.
- Le `FightManager` doit avoir une méthode `createMonster` qui instancie un nouvel objet `Monster` avec un nom de votre choix et ses points de vie à 100 également.
- Vous pouvez par ailleurs préparer la méthode `fight()` en la laissant vide pour le moment.

### fight.php et FightManager.php

De retour dans `fight.php`, nous allons pouvoir préparer le combat. Nous avons déjà instancié le Hero grâce à la méthode `find` de son manager. Il ne nous reste plus qu'à créer le monstre puis d'exécuter la méthode `fight()`.

Instanciez un nouveau `FightManager`, puis appelez la méthode `createMonster()` et ensuite appelez la méthode `fight()` qui prendra en argument un objet `$hero` et un objet `$monster`.

Une fois le combat résolu, appelez une nouvelle méthode du CRUD de `HeroesManager` : `update()` que nous allons aborder plus loin.

Votre code de résolution du combat sur `fight.php` doit ressembler à cela :

<img src="./assets/RésolutionFightPHP.png" alt="resolution-fight.php">

La méthode `fight()` est le cœur de votre application. Elle prend en argument un objet `Hero` et un objet `Monster`. Le but de cette méthode est de créer un tableau vide, puis, dans une boucle, retirer des points de vie au héros (le monstre tape en premier). Si le héros survit au `hit()` du monstre, <br>le combat continue :`$hero->hit($monster);`.

Comme vous le voyez, on peut lire le code comme si on parlait normalement avec la POO. Et c’est là le but intrinsèque de ce paradigme.

Cette méthode retourne un tableau contenant en 'string' le déroulé du combat qu’on pourra ensuite afficher dans `fight.php` avec une simple boucle foreach.

### Mise à jour du Combat dans `Hero.php`

La méthode `fight` ne peut, pour l’instant, pas fonctionner tant que la fonction `hit()` de Hero et Monster n’est pas définie.

Nous allons faire simple. L’argument de cette fonction est un objet Monster dans la classe Hero et inversement pour le Monster qui prendra un objet hero en argument de cette fonction.

La logique est ensuite la même : on récupère les points de vie de l’entité en argument, puis on lui retire un chiffre aléatoire, et on utilise le setter pour actualiser ses HP.

<img src="./assets/HitPHP.png" alt="hit.php">

### HeroesManager.php

Il ne reste plus qu'à créer la méthode `update` de notre HeroesManager, qui prend en argument l'objet Hero qui vient de passer dans la méthode `fight`. c'est l'étape cruciale qui permet d'enregistrer tout ce qu'il vient de se passer dans le FightManager.

Il s'agit d'une simple requête `UPDATE heroes SET health_points = :health_points WHERE id = :id` et vous utilisez les getters de l'objet Hero dans le execute.

**À ce stade, tout est censé fonctionner. Un Hero peut être créé, on peut le sélectionner pour déclencher un combat contre un monstre généré à la volé, les points de vie de notre Héro passent dans la moulinette du programme et est ensuite enregistré dans notre base de donnée.**

## Ressources

- [Déclaration de Classe en PHP](https://tutowebdesign.com/declaration-class-php.php)
- [Visibilité des Propriétés et Méthodes en PHP](https://tutowebdesign.com/visibilite-classe-php.php)
- [Héritage en PHP](https://tutowebdesign.com/heritage-objet-php.php)

Pour un cours plus approfondi sur la Programmation Orientée Objet (POO) en PHP, vous pouvez consulter l'ensemble du cours disponible [ici](https://tutowebdesign.com/poo-php.php).

## 🏆 Objectifs sprint 2

### Amélioration de la Qualité du Code
- Respect des normes PHP PSR-1 et PSR-2:
  - [PSR-1](https://www.php-fig.org/psr/psr-1/)
  - [PSR-2](https://www.php-fig.org/psr/psr-2/)
- Refactorisation et découpage du code pour garantir la scalabilité et la maintenabilité.

### Nouvelles Fonctionnalités
- Choix de classe à la création du héros : guerrier, mage ou archer.
- Attribution automatique de types aux monstres : ogre, sorcier, fantassin.
- Interactions spécifiques entre les classes :
  - Un guerrier reçoit 2x plus de dégâts lorsqu'attaqué par un sorcier.
  - Un archer reçoit 2x plus de dégâts lorsqu'attaqué par un ogre.
  - Un mage reçoit 2x plus de dégâts lorsqu'attaqué par un fantassin.
- Chaque classe a une attaque spéciale en plus de l'attaque commune.
- Ajout d'un niveau d'énergie pour les héros, avec récupération automatique partielle à chaque tour.

### Bonus
- Ajout d'effets supplémentaires aux attaques spéciales : gel, poison, affaiblissement...
- Intégration de nouvelles attaques avec différents effets et coûts en énergie.
- Introduction d'un système de tour par tour
