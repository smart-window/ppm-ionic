                        The ionCube Loader Wizard
                        -------------------------

PACKAGE CONTENTS
----------------

This package contains:

** a Loader Wizard script to assist with ionCube Loader installation 
(loader-wizard.php).

** this README.txt file.


PURPOSE OF THE LOADER WIZARD
----------------------------

The ionCube Loader Wizard assists in the installation of the appropriate 
ionCube Loader on a web server. The ionCube Loader is necessary to run 
PHP files that have been encoded with the ionCube Encoder.


USING THE LOADER WIZARD
-----------------------

1. Upload the contents of this package to a directory/folder called ioncube
that is in your main web space. On a dedicated or VPS system this can be the
root web directory of any configured domain on the system. 

2. Launch the Loader Wizard script in your browser. For example:
         http://yourdomain/ioncube/loader-wizard.php

3. Follow the steps in the Loader Wizard to install the appropriate Loaders 
for your system.

4. If the Loader Wizard is not found, carefully check the location to where 
you uploaded the wizard script on your server.

5. If there is any problem with the Loader Wizard script, please raise a 
support ticket at http://support.ioncube.com


WHERE TO INSTALL THE LOADERS
----------------------------

The Loader Wizard should be used to guide the installation process but the
following are the standard locations for the Loader files. Loader file
packages can be obtained from http://www.ioncube.com/loaders.php. 
Please check that you have the correct package of Loaders for your system.

** Installing to a remote SHARED server

- Upload the Loader files to a directory/folder called ioncube within your 
main web scripts area.  
(This will probably be where you placed the loader-wizard.php script.)


** Installing to a remote UNIX/LINUX DEDICATED or VPS server

- Upload the Loader files to the PHP extensions directory or, if that is 
not set, /usr/local/ioncube .


** Installing to a remote WINDOWS DEDICATED or VPS server

- Upload the Loader files to the PHP extensions directory or, if that is 
not set, C:\windows\system32 .


64-BIT LOADERS FOR WINDOWS
--------------------------

64-bit Loaders for Windows are available for PHP 5.5 upwards. 
The Loader Wizard will not give directions for installing 64-bit Loaders for
any earlier version of PHP 5.5. 

Copyright (c) 2002-2016 ionCube Ltd.                  Last revised 15-Sep-2016
