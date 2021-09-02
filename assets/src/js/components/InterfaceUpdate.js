import $ from 'jquery';

class InterfaceUpdate {
  constructor() {
    this.init();
  }

  init() {
    // Images
    $('img').each(function() {
      if (!$(this).hasClass('emoticon')) {
        if ($(this).attr('src').indexOf('.png') !== -1) {
          let width = $(this).data('width')
          $(this).css({
            'width': width + 'px',
            'height': 'auto'
          });
          $(this).addClass('rounded img-thumbnail');
        } else {
          $(this).addClass('img-fluid');
        }
      }
    });

    // Tables
    $('table').each(function() {
      $(this).addClass('table table-bordered');
    });

    // Table of contents
    $('.toc-macro').each(function() {
      $(this).prev().remove();
    });

    // Information macros
    $('.confluence-information-macro').each(function() {
      $(this).addClass('alert alert-primary');
    });

    // Links
    $('a').each(function() {
      let href = $(this).attr('href');
      // Force external links to open in new windows
      if (href && href.indexOf('http') !== -1) {
        $(this).attr('target', '_blank');
      }
      // Force any links pointing to Confluence to go nowhere
      if (href && href.indexOf('adcetera.atlassian.net') !== -1) {
        $(this).attr('href', 'javascript:void(0);');
      }
    })
  }

}

export default InterfaceUpdate;
