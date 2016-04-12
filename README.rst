.. _start:

*************
Documentation
*************

This extension adds a new custom page type used to display a custom 404 error page.

Configuration
-------------

Simply install the extension and create a new page with your error message.

No TypoScript setup to include.

You can use following markers in your content:

:###CURRENT_URL###: The url of the called page.
:###REASON###: A text why the error occured.

If you need, you can change the page type in the extension configuration.

.. note:: If you change the page type, you must update the doktype of your previously created error pages by yourself.

