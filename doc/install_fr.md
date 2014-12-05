## Pré-installation

AlternC est prévu pour fonctionner sur la dernière ou l'avant-dernière version stable de la distribution Linux Debian. Nous utiliserons donc `apt-get` pour installer les logiciels.

Pour installer Alternc vous devez : 

* avoir un accès SSH à ce serveur.
* Avoir les droits d'administrateur (`sudo -s` ou `su`)

### ACL

AlternC dépend des acls noyau afin de gérer les droits utilisateurs notamment en ce qui concerne les dossiers web. Il est donc nécessaire d'installer le paquet `acl` avec :

```
apt-get install acl
```

Il faut ensuite indiquer au système la partition qui va contenir les données utilisateurs en y activant les ACLs. Pour ce faire, on peut modifier le fichier /etc/fstab en rajoutant l'option "acl" à la partition concerné:

    nano /etc/fstab
    UUID=71822887-fedb-4d95-a9cc-0841dcc8944f /               ext3    acl,errors=remount-ro 0       1

### Quota

AlternC peut également gérer les quotas disques des utilisateurs.Contrairement aux ACLs, les quotas ne sont pas nécessaires au fonctionnement d'AlternC. S'ils ne sont pas activé ou installé, AlternC considérera simplement que les quotas sont infinis pour chaque utilisateur. Pour ce faire il faut installer le paquet quota :

```
apt-get install quota
```

Et encore une fois modifier le fstab pour indiquer leur activation :

```
nano /etc/fstab
# / was on /dev/sda1 during installation
UUID=71822887-fedb-4d95-a9cc-0841dcc8944f /               ext3    acl,grpquota,errors=remount-ro 0       1
```

Remontage de la partition

Une fois ces modification effectués, il suffit de remonter la partition concerné -en supposant toujours que c'est la partion "/" qui contiendra les données utilisateurs- avec :

```
mount -o remount /
```

### MySQL

Si vous voulez héberger le serveur mysql sur la même machine, installez d'abord mysql-server :

```
apt-get install mysql-server
```

> IMPORTANT : Entrez un mot de passe administrateur et NOTEZ-LE car il vous sera demandé en cours d'installation.
Configuration des dépôts alternc sur votre serveur</blockquote>

## Installation

L'installation d'Alternc est connue et supportée pour le système Debian GNU/Linux, qui utilise le système de gestion de paquets ​apt.

### À partir d'un dépot pour Debian

Pour installer AlternC sur un serveur vous devez utiliser un éditeur de texte pour ajouter la source de package officielle d'AlternC :

* soit dans le fichier existant `/etc/apt/sources.list`
* soit dans un nouveau fichier `/etc/apt/sources.list.d/alternc.list`

les lignes suivantes :

```
deb http://debian.alternc.org/ stable main
deb-src http://debian.alternc.org/ stable main
```

Les paquets debian sont signés numériquement, avant d'exécuter un apt-get update, il convient d'ajouter la clef du repository alternc avec la commande:

```
wget http://debian.alternc.org/key.txt -O - | apt-key add -
```

Il s'agit d'une clé PGP possédée et maintenue par les développeurs ayant le droit d'écrire dans le dépot sur debian.AlternC.org.

Ensuite, mettez à jour la liste des packages disponibles pour apt :

```
apt-get update
```

Il ne reste qu'à lancer la commande d'installation d'alternc

```
apt-get install alternc
```


### Écrans d'installation

Une succession d'écrans vous permet de saisir les informations sur la configuration de votre serveur. Si certains choix par défaut sont sans souci, d'autres nécessitent toute votre attention.
Important : Serveurs de nom

Les serveurs de noms servent à distribuer l'information sur les noms de domaine installés sur votre serveur. Si vous avez besoin de serveurs de noms, Alternc vous propose un service gratuit sur alternc.net en dans ce cas vous pouvez saisir :

* DNS primaire : ns1.alternc.net
* DNS secondaire : ns2.alternc.net

**Important** : Nom de domaine du serveur

Attention, si vous avez un nom de domaine que vous comptez utiliser pour votre compte, ne l'indiquez pas dans cet écran. En effet, ce nom de domaine sera alors la "porte d'accès" à Alternc.

Utilisez un autre nom de domaine pointant sur votre machine voire directement son adresse IP.

En résumé, si votre serveur est sur l'IP 12.34.56.78 et que vous avez le nom de domaine toto.com pour votre site perso, n'utilisez pas toto.com mais soit l'adresse IP, soit alternc.toto.com, soit le domaine fourni par votre hébergeur ex: serveur234215.groshebergeur.net

Pour information, Alternc prépare un service sur alternc.net qui vous permettra d'annoncer votre serveur sur un domaine en .alternc.net
Défaut OK : phpMyAdmin

Pas besoin de configurer pour un service, alternc s'occupe de configurer l'URL à laquelle phpMyAdmin sera accessible
Défaut OK : Postfix

Choisir "Site Internet", puis suivre les instructions
Post Installation

Une fois que l'installation est achevée, le script alternc.install doit être exécuté. Il va générer notamment les configurations de votre serveur pour qu'Alternc fonctionne.

```
alternc.install
```

Premier login

Vous pouvez désormais accéder au panel Alternc sur le nom de domaine ou l'IP que vous avez donné. Vous devriez voir une page de login dont l'accès par défaut -que vous voudrez sans doute changer immédiatement- est :

user: `admin`
pass: `admin`

## Plugins

### Roundcube

Pour installer roundcube pour alternc, il suffit d'installer le paquet `alternc-roundcube` puis de mettre à jour AlternC :

```
apt-get install alternc-roundcube
alternc.install
```

### Mailman

Pour installer la liste de diffusion Mailman, il suffit d'installer le paquet `alternc-mailman` puis de mettre à jour AlternC :

```
apt-get install alternc-mailman
alternc.install
```

## Mettre à jour sa version d'AlternC

### Branche 3.x

### Branche 1.x vers 3.x 

#### AVANT l'installation:

* Les pré-requis du chapitre précédent: "Installer", doivent être respectés (acl, quotas, ...).
* Vous devez désactiver le plugin procmailbuilder de Squirrelmail si vous avez un squirrelmail déjà installé, et que ce plugin est activé. Pour cela, exécutez /etc/squirrelmail/conf.pl, puis vous devez taper le numéro correspondant au menu "Plugins" afin d'obtenir la liste des plugins installés/disponibles. Si le plugin procmailbuilder n'apparaît pas du tout, c'est parce qu'il n'existe plus, donc tout va bien. Sinon, s'il apparaît dans la liste "Installed Plugins", entrez le numéro correspondant et tapez Entrée, celui-ci devrait passer dans la liste "Available Plugins", et celui-ci se retrouve donc désactivé. 

#### PENDANT l'installation:

* L'installateur Debian vous demandera s'il faut écraser les fichiers de conf modifiés depuis l'installation précédente, répondez OUI sinon rien ne fonctionnera... 

#### APRÈS l'installation :

* Vous devrez lancer manuellement le script de migration des fichiers procmail en sieve (filtrage des mails): /usr/lib/alternc/procmail_to_sieve.php
Parfois, même après suppression du paquet, courier imap ne s'arrête pas... Veillez à ce que le démon ne s'execute pas, le cas contraire tuez-le. 
* En ce qui concerne le webmail :
Vous devez installer un `alternc-squirrelmail` ou `alternc-roundcube` si vous voulez gérer un webmail avec AlternC. 
`alternc-roundcube` a besoin du paquet `roundcube` des backports de Squeeze : 

```
aptitude install -t squeeze-backports alternc-roundcube
```