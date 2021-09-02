import './../css/libs.scss';
import './../css/style.scss';

import '@babel/polyfill/dist/polyfill';
import 'whatwg-fetch/dist/fetch.umd';
import $ from 'jquery';
import './popper.min';
import 'bootstrap/dist/js/bootstrap';

import Navbar from "./components/Navbar";
import Navigation from './components/Navigation';
import InterfaceUpdate from "./components/InterfaceUpdate";

$(document).ready(function() {

  init();

  function init() {
    // Add font-awesome
    let style = document.createElement('link');
    style.rel = 'stylesheet';
    style.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css';
    document.head.appendChild(style);

    // Init navbar
    let navbar = new Navbar();

    // Init navigation
    let nav = new Navigation();

    // Init interface
    let ui = new InterfaceUpdate();
  }

  function handleRendering() {
    // Check for page attachments and hide
    let $attachments = $('#attachments');
    if ($attachments.length > 0) {
      $attachments.parents('div.pageSection').remove();
    }

    // Remove "Space Details" section
    let $spaceDetails = $('#title-text');
    if ($spaceDetails.length > 0) {
      if ($spaceDetails.text().toLowerCase().trim() === 'space details:') {
        $('#main-header, #main-content, .pageSectionHeader').remove();
      }
    }

    // Finally, show the page
    showPage();
  }
  handleRendering();

  function showPage() {
    // Trigger parent frame to show
    let frame = window.parent.document.getElementById('srcFrame');
    $(frame).show();
  }
});
