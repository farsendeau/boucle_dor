/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
import './styles/app.css';
window.bootstrap = bootstrap;
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
