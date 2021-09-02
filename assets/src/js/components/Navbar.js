import $ from 'jquery';

let tmplNavBar = require("../templates/navbar.handlebars");

class Navbar {
  constructor() {
    this.render();
  }

  render() {
    $('body').prepend(
      tmplNavBar()
    );
  }

}

export default Navbar;
