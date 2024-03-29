# ![Music Intelligence Fm logo](/assets/imgs/music-intelligence-fm.png)

- French version of this README is available [here](README.fr.md).
- Demo will be available soon [here](https://music-intelligence-fm.yann.red/).

## Description
Music Intelligence Fm is a web application using the Last.fm API and allowing to manage your scrobbles and your statistics.

>*What is Last.fm ?*
>>Last.fm is a music-oriented website and online platform that serves multiple purposes related to music discovery, recommendation, and social interaction.
The main functionalitie of Last.fm is scrobbling

>*What is Scrobbling ?*
>>Scrobbling is the process of tracking the music you listen to. The Last.fm app on your computer or phone will scrobble the music you play on your device (streaming app like Spotify or media player like Winamp). This means that when you listen to a song, the name of the song is sent to Last.fm and added to your music profile.

>*And about Music Intelligence Fm ?*
>>#### Music Intelligence Fm offers to complete the Last.fm features by allowing complete management of its own statistics.

## Features of Music Intelligence Fm

- Import scrobbles from Last.fm.
- Daily scrobble synchronization (in progress...).
- Advanced scrobble search.
- Top artists, albums, tracks, genres.
- Customizable statistics dashboard.

Upcoming features :

- Favorites management.
- Playlist management.
- Removed scrobbles.
- Statistics reports by email.
- Anomalies report after new scrobbles import.
- Duplicate detection and merge features.
- Generation of playlists for Squeezebox.

## Tech Stack

### Languages

- [PHP 8.3](https://www.php.net/)
- [JavaScript ES6](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
- [MySQL](https://www.mysql.com/)

### Frameworks

- [Symfony 7](https://symfony.com/)

### Back-end Libraries

- Symfony main bundles included with the Webapp skeleton
- [KNP Paginator Bundle](https://github.com/KnpLabs/KnpPaginatorBundle)
- [Symfony Messenger Bundle](https://symfony.com/doc/current/components/messenger.html)

### Front-end Libraries

- [Tailwind CSS](https://tailwindcss.com/)
- [Chart.js](https://www.chartjs.org/)
- [Gridstack.js](https://gridstackjs.com/)
