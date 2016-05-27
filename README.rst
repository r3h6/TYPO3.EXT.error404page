.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. _start:

=============
Documentation
=============

Custom error 404 pages made simple. Use TYPO3 pages for display error documents. Works for multi domain and multilingual installations.

This extension adds a new custom page type for rendering custom 404 error pages.


Screenshots
-----------

.. figure:: ./Documentation/Images/ModulPage.png
   :alt: New page type.
   :width: 200px

.. figure:: ./Documentation/Images/ModulStatistic.png
   :alt: Optional statistic backend modul.
   :width: 200px


Usage
-----

Simply install the extension and create a new page with your error message.

You can use following markers in your content:

:###CURRENT_URL###: The url of the called page.
:###REASON###: A text why the error occured.


Configuration
-------------

* No TypoScript setup to include.
* You can enable the error log and statistic backend modul in the extension configuration.
* If required, you can change the page type in the extension configuration.

.. warning::

    If you change the page type, you must update the doktype of your previously created error pages by yourself.


Statistic
---------

If enabled, the last 100'000 errors are logged and listed in the backend modul "Error statistic".


Contributing
------------

Bug reports and pull request are welcome through `GitHub <https://github.com/r3h6/TYPO3.EXT.error404page/>`_.