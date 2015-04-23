# auth0-joomla
Joomla extension focused on Single Sign On for Enterprises + Social Login + User/Passwords. Powered by Auth0.

##Instalation

1. Download from [here](https://github.com/auth0/auth0-joomla/releases) the latest release of the extension.
2. Access to the Joomla administrator interface and then upload the zip file to the Extension Manager.
3. If you don't have an Auth0 account, you can [create one](http://auth0.com) for free).
4. Go to the Auth0 component (on the top toolbar, under the components menu) and the click on options.
5. Complete your app information (you can create one or get the information [here](https://manage.auth0.com/#/applications)).
6. Go to the Module Manager and publish the Auth0 module and after that, access to the module configuration.
7. Select the module position on the sidebar and enable it for all pages in the "Menu Assignment".
8. It is done!

##User Management configuration

You can enable and disable the user signup and select the default user group on the User Managent Configuration.

To access it, go to the System menu on the top toolbar, then click on Global Configuration.
On the left sidebar, click on User Management and there you will find both settings **Allow User Registration** and **Allow User Registration**.

##Lock customization

Under the Auth0 Module configuration, there is lots of settings to customize the Lock widget.

Under Module tab:
- Show login form: enable or disable if Lock is visible
- Show as modal: if it is enabled, it will show a button that, when clicked, will show lock as a modal over the content of the page
- Form title: Lock title
- Icon URL: it should be a URL to the image to show over the title
- Enable Gravatar integration: if enabled, will show the user avatar on the login widget
- Customize the Login Widget CSS: it will enable you to change the Lock appearance.
- Show bit social buttons: it will change the way the buttons are shown on Lock

Under Advanced Settings tab:
- Widget URL: you can change it to force to use a certain version of the Lock widget
- Translation: should be a the language or a valid json with the translation (see https://github.com/auth0/lock/wiki/Auth0Lock-customization#dict-stringobject)
- Username style: let you change if you will use only email or username on the login form
- Remember last login: will enable a short hand login when the user come back to the site


## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## Author

* [Auth0](auth0.com)

## License

This project is licensed under the MIT license. See the [LICENSE](LICENSE) file for more info.
