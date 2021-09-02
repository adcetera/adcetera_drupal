import $ from 'jquery';

let tmplNavWrapper = require("../templates/navigation.handlebars");
const menuKey = 'adcetera_drupal.currentSideMenu';
const menuFlag = 'adcetera_drupal.menuFlag';

class Navigation {
  constructor() {
    this.getData();
  }

  getData() {
    let currentSideMenu = window.localStorage.getItem(menuKey);
    if (currentSideMenu === null) {
      let menuStart = $('h2.pageSectionTitle');
      if ($(menuStart).text().trim().toLowerCase() === 'available pages:') {
        let menuMarkup = '';
        try {
          menuMarkup = $(menuStart).parents('div.pageSection').children('ul').html();
        } catch (err) {
        }
        if (menuMarkup !== '') {
          window.localStorage.setItem(menuKey, menuMarkup);
          this.render(menuMarkup)
        }
      }
    } else {
      this.render(currentSideMenu);
    }
  }

  render(markup) {
    $('body')
      .append(
        tmplNavWrapper({
          'Navigation': markup
        })
      );
    this.bind();
    this.setActiveNavItem();
  }

  bind() {
    // Style guide navigation
    let sgNavTrigger = $(".js-nav-styleguide-trigger"),
      sgNavTarget = $(".js-nav-styleguide-target");

    if (window.localStorage.getItem(menuFlag) === null) {
      setTimeout(function() {
        sgNavTrigger.removeClass('open').attr('title', 'Click to open');
        sgNavTrigger.find('i').removeClass('fa-times');
        sgNavTrigger.find('i').addClass('fa-bars');
        sgNavTarget.removeClass('open');
        window.localStorage.setItem(menuFlag, 'true');
      }, 2000);
    } else {
      sgNavTrigger.removeClass('open').attr('title', 'Click to open');
      sgNavTrigger.find('i').removeClass('fa-times');
      sgNavTrigger.find('i').addClass('fa-bars');
      sgNavTarget.removeClass('open');
    }

    function openNav(e) {
      e.preventDefault();

      if( sgNavTrigger.hasClass('open') ) {
        sgNavTrigger.removeClass('open').attr('title', 'Click to open');
        sgNavTrigger.find('i').removeClass('fa-times');
        sgNavTrigger.find('i').addClass('fa-bars');
        sgNavTarget.removeClass('open');
      } else {
        sgNavTrigger.addClass('open').attr('title', 'Click to close');
        sgNavTrigger.find('i').removeClass('fa-bars');
        sgNavTrigger.find('i').addClass('fa-times');
        sgNavTarget.addClass('open');
      }
    }
    sgNavTrigger.on('click', openNav);
  }

  setActiveNavItem() {
    let currentPage = window.location.pathname;
    if (currentPage.indexOf('/') !== -1) {
      // Extract the last path
      currentPage = currentPage.split('/');
      currentPage = currentPage[currentPage.length -1];

      if (currentPage) {
        $('.nav-styleguide')
          .find('a[href="' + currentPage + '"]')
          .addClass('nav-active');
      }
    }
  }
}

export default Navigation;
