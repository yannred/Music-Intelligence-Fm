import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './styles/app.css';

// GridStack & widgets
import './styles/gridstack.css';

// Jquery
import jquery from 'jquery';
const $ = jquery;
window.$ = window.jQuery = $;

// Flowbite
import 'flowbite/dist/flowbite.min.css';
import 'flowbite';