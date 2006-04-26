<?php
    require_once "includes/header.inc.php";
?>
        <h3 class="titre">Gestion des domaines</h3>
        <div class="menu-actions">
            <a href="?action=ajouter"><img src="../images/icons/actions/ajouter.png" alt="" title="Ajouter" /></a>
            <a href="?action=lister"><img src="../images/icons/actions/lister.png" alt="" title="Lister" /></a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="?action=aider"><img src="../images/icons/actions/aider.png" alt="" title="Aider" /></a> 
        </div>
        <p>Cette page permet d'ajouter, supprimer, modifier un domaine. Lors de l'ajout
        du domaine, un assistant apparait demandant le domaine. Ensuite des checkboxs
        demandent comment g&eacute;rer le domaine</p>
        <ul>
            <li><strong>Gestion DNS :</strong> Permet au domaine d'&ecirc;tre g&eacute;r&eacute; au niveau DNS (MX, NS, A, CNAME, AAA ...)</li>
            <li><strong>Gestion WEB :</strong> Permet au domaine d'&ecirc;tre g&eacute;r&eacute; au niveau WEB (vhosts, redirection, comptes FTP et WebDAV)</li> 
            <li><strong>Gestion MAIL :</strong> Permet au domaine d'&ecirc;tre g&eacute;r&eacute; au niveau MAIL (comptes pop/imap, smtp, alias, redirections et listes de diffusion)</li>       
        </ul>
        <p>Pour la gestion DNS, il est possible d'avoir le choix encore DNS primaire et secondaire. Le script
        v&eacute;rifie si le domaine existe, puis v&eacute;rifie si le NS primaire/secondaire conrespond 
        &agrave; l'IP du serveur dans le cas d'une gestion ma&icirc;tre ou esclave.</p>
<?php
    require_once "includes/footer.inc.php";
?>
