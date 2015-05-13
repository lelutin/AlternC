

Pré-installation
================

AlternC est prévu pour fonctionner sur la distribution Linux Debian Wheezy.

Pour installer Alternc vous devez : 

* avoir un accès SSH à ce serveur.
* Avoir les droits d'administrateur (`sudo -s` ou `su`)
* vérifier que `#includedir /etc/sudoers.d` est présent dans `/etc/sudoers` avec la commande `visudo`.


ACL
---

AlternC dépend des acls noyau afin de gérer les droits utilisateurs notamment en ce qui concerne les dossiers web. Il est donc nécessaire d'installer le paquet `acl` avec :

```
apt-get install acl
```

Il faut ensuite indiquer au système la partition qui va contenir les données utilisateurs en y activant les ACLs. Pour ce faire, on peut modifier le fichier `/etc/fstab` en rajoutant l'option "acl" à la partition concerné, par exemple :

```
/dev/md1    /    ext3    auto,noatime,acl    0    0
```

> À noter que c'est `acl` pour ext3, et `attr2` pour xfs.


Quota
-----

AlternC peut également gérer les quotas disques des utilisateurs.Contrairement aux ACLs, les quotas ne sont pas nécessaires au fonctionnement d'AlternC. S'ils ne sont pas activé ou installé, AlternC considérera simplement que les quotas sont infinis pour chaque utilisateur. Pour ce faire il faut installer le paquet quota :

```
apt-get install quota
```

Et encore une fois modifier le `/etc/fstab` pour indiquer leur activation :

```
/dev/md1 /               ext3    acl,grpquota,errors=remount-ro 0       1
```

Remontage de la partition
-------------------------

Une fois ces modification effectués, il suffit de remonter la partition concerné -en supposant toujours que c'est la partion `/` qui contiendra les données utilisateurs avec :

```
mount -o remount /
```



Installation
============

MySQL
-----

Il est nécessaire d'installer MySQL pour pouvoir utiliser AlternC. Si vous voulez l'héberger sur la même machine, vous pouvez le faire avec la commande suivante :

```
apt-get install mysql-server
```

> IMPORTANT : Entrez un mot de passe administrateur et NOTEZ-LE car il vous sera demandé en cours d'installation.

Configuration des dépôts
------------------------


> L'installation d'Alternc est connue et supportée pour le système Debian GNU/Linux, qui utilise le système de gestion de paquets ​apt.


Pour installer AlternC sur un serveur vous devez utiliser un éditeur de texte pour ajouter la source des packages d'AlternC :

```
deb http://debian.alternc.org/ stable main
deb-src http://debian.alternc.org/ stable main
```

dans un des deux fichiers suivants :

* soit dans le fichier existant `/etc/apt/sources.list`
* soit dans un nouveau fichier `/etc/apt/sources.list.d/alternc.list`


Les paquets debian sont signés numériquement, avant d'exécuter un apt-get update, il convient d'ajouter la clef du repository alternc avec la commande:

```
wget http://debian.alternc.org/key.txt -O - | apt-key add -
```

Il s'agit d'une clé PGP possédée et maintenue par les développeurs ayant le droit d'écrire dans le dépot sur debian.AlternC.org.

Ensuite, mettez à jour la liste des packages disponibles pour apt :

```
apt-get update
```

Il ne reste qu'à lancer la commande pour installer alternc (n'oubliez pas la partie *finaliser l'installation* après cette opération) :

```
apt-get install alternc alternc-ssl alternc-api
```

Vous pouvez y ajouter `alternc-mailman` pour générer des mailing-list, `alternc-roundcube` pour avoir un webmail, et `alternc-awstats` pour générer des statistiques pour vos sites web.

DNS - serveurs de nom
---------------------

Les serveurs de noms servent à distribuer l'information sur les noms de domaine installés sur votre serveur. Si vous avez besoin de serveurs de noms, Alternc vous propose un service gratuit sur alternc.net en dans ce cas vous pouvez saisir :

* DNS primaire : ns1.alternc.net
* DNS secondaire : ns2.alternc.net



Mailing-list / Mailman
----------------------

Pour installer la liste de diffusion Mailman, il suffit d'installer le paquet `alternc-mailman` puis de passer à la partie *finaliser l'installation* :

```
apt-get install alternc-mailman
```

Stats / [Awstats](http://www.awstats.org/)
---------------

Si vous souhaitez générer facilement des statistiques pour vos sites web, il suffit d'installer le paquet `alternc-awstats` puis de passer à la partie *finaliser l'installation* :

```
apt-get install alternc-awstats
```

Webmail / [Roundcube](https://roundcube.net/)
-------------------

Si vous souhaitez avoir un webmail pour accéder à vos mails via votre navigateur, il suffit d'installer le paquet `alternc-roundcube` puis de passer à la partie *finaliser l'installation* :

```
apt-get install alternc-roundcube
```




nom de domaine du serveur
-------------------------

Attention, si vous avez un nom de domaine que vous comptez utiliser pour votre compte, ne l'indiquez pas dans cet écran. En effet, ce nom de domaine sera alors la "porte d'accès" à Alternc.

Utilisez un autre nom de domaine pointant sur votre machine voire directement son adresse IP.

En résumé, si votre serveur est sur l'IP 12.34.56.78 et que vous avez le nom de domaine exemple.com pour votre site perso, n'utilisez pas exemple.com mais soit l'adresse IP, soit alternc.exemple.com, soit le domaine fourni par votre hébergeur ex: serveur234215.groshebergeur.net

Pour information, Alternc prépare un service sur alternc.net qui vous permettra d'annoncer votre serveur sur un domaine en .alternc.net

> Défaut OK : phpMyAdmin

Pas besoin de configurer pour un service, alternc s'occupe de configurer l'URL à laquelle phpMyAdmin sera accessible

> Défaut OK : Postfix

Choisir "Site Internet", puis suivre les instructions

finaliser l'installation
------------------------

Une fois que l'installation est achevée, le script `alternc.install` doit être exécuté. Il va générer notamment les configurations de votre serveur pour qu'Alternc fonctionne.

```
alternc.install
```

Post Installation
=================

Première connexion
-------------

Vous pouvez désormais accéder au panel Alternc sur le nom de domaine ou l'IP que vous avez donné. Vous devriez voir une page de login dont l'accès par défaut que vous voudrez sans doute changer *immédiatement* est :

* identifiant : `admin`
* mot de passe : `admin`
