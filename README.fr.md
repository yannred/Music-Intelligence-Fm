# ![Music Intelligence Fm logo](/assets/imgs/music-intelligence-fm.png)

- La version anglaise de ce README est disponible [ici](README.md).
- Une démo sera bientôt disponible [ici](https://music-intelligence-fm.yann.red/).

## Description
Music Intelligence Fm est une application web utilisant l'API Last.fm et permettant de gérer vos scrobbles et vos statistiques.

>*Last.fm c'est quoi ?*
>>Last.fm est une plateforme en ligne qui propose de multiples services liés à l'écoute et aux bibliothèques musicales.
La fonctionnalité principale de Last.fm est le scrobbling.

>*Qu'est-ce que le Scrobbling ?*
>>C'est une fonctionnalité de suivi de la musique que vous écoutez. L'application Last.fm sur votre ordinateur ou votre téléphone "scrobblera" la musique que vous écoutez 
>>sur votre appareil (application de streaming comme Spotify ou lecteur multimédia comme Winamp). Cela signifie que lorsque vous écoutez une chanson, le nom de la chanson est envoyé
> à Last.fm et ajouté à votre profil musical.  
  

>*Et Music Intelligence Fm là dedans ?*
>>#### Music Intelligence Fm propose de compléter les fonctionnalités de Last.fm en permettant une gestion complète de ses statistiques.

## Features of Music Intelligence Fm

- Import des scrobbles à partir de Last.fm.
- Synchronisation quotidienne des scrobbles.
- Recherche avancée de scrobbles.
- Top artistes, albums, morceaux, genres.
- Dashboard de statistiques personnalisable.

Prochainement :

- Script bash pour une installation facile sur Raspberry Pi.
- Gestion des favoris.
- Gestion des playlists.
- Suppression de scrobbles.
- Rapports de statistiques par mail.
- Rapport d'anomalies d'import de nouveaux scrobbles.
- Detection des doublons et fonctionnalité de fusion.
- Génération de playlists pour Squeezebox. 

## Stack technique

### Langages principaux

- [PHP 8.3](https://www.php.net/)
- [JavaScript ES6](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- [MySQL](https://www.mysql.com/)

### Frameworks

- [Symfony 7](https://symfony.com/)

### Librairies Back-end

- Symfony main bundles (Doctrine ORM, Twig, Security, Form, Asset-mapper)
- [Symfony UX Turbo Bundle for Turbo Frames](https://symfony.com/bundles/ux-turbo)
- [KNP Paginator Bundle](https://github.com/KnpLabs/KnpPaginatorBundle)
- [Symfony Messenger Bundle](https://symfony.com/doc/current/components/messenger.html)

### Librairies Front-end

- [Tailwind CSS](https://tailwindcss.com/)
- [Hotwired Turbo](https://turbo.hotwired.dev/)
- [Jquery](https://jquery.com/)
- [Chart.js](https://www.chartjs.org/)
- [Gridstack.js](https://gridstackjs.com/)