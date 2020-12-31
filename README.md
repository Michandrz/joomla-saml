joomla-onelogin
===============

Joomla 3.3 SAML Authentication plugin based on OneLogin PHP SAML Toolkit.

This plugin enables your Joomla users to log in through SAML.

joomlsa-saml version 1.6.0 updates php-saml library to 2.15.0 (it includes XEE attack prevention).
Previous versions are vulnerable.

joomlsa-saml version 1.3.0 updates php-saml library to 2.10.0 (it includes SAML Signature Wrapping attack prevention and other security improvements).
Previous versions are vulnerable.

Features
--------

* Single sign on
* Single log out
* Just in time provisioning
* Supports groups
* Supports attribute passing


Pre-requisites
--------------

Take a look on the php saml toolkit dependences:
https://github.com/onelogin/php-saml#dependences


Installation
------------

At the admin interface, click on Extensions > Extension Manager. 
Unlike previous versions there is now a single pkg_onelogin_php-saml_joomla.zip to upload and install.


Settings
--------

At the admin interface, click on Extensions > Plugin Manager. Search "Onelogin". 
Enable both plugins by clicking on the applicable boxes. Click on the name of the user plugin. 
At the "Description" tab you will find info describing the rest of the tabs. Once the settings are set, turn enable the plugin.

The metadata of the Joomla SP will be available at
``` 
http://<path to joomla/index.php?option=com_oneloginsaml&task=getMetadata&format=xml
```

How to add "SAML Login" link
----------------------------

If you are using the Plg_content and the standard login form, it will automatically add SAMLLogin Links.

To manually add them:
The "SAML Login" link can be added in at least 2 different places:

* Add the link to the "Login Form module". At the admin interface, click on Extensions > Module Manager and search the
   word "Login", in the result you can find the active modules that currently are rendering a Login Form. Edit them and in the
   pre-text add the following:

```
   <a href="http://<path to joomla>/index.php?option=com_login&task=samlLogin">SAML Login</a>
```

* Add the link to the main login form (Component User, View login). At the admin interface, click on Extensions > Module Manager
   and edit the "Site" templates that are currently used. Click on "Create Overrides" and select at "Components" the "com_users" > "login". Later click on the editor and edit html > com_users > login > default_login.php. You will see the a mix of php and html, search the line around 78 and after the JLOGIN button set:

``` 
   <a href="http://<path to joomla>/index.php?option=com_login&task=samlLogin" style="padding-left:20px;">SAML Login</a>
```

Local Login
-----------

When SAML enabled, you can always continue login through other login Plugins.

You can disable local login by removing the joomla authentication plugin. We do not recomend this as you will have to go to the database to rescue the system. 
