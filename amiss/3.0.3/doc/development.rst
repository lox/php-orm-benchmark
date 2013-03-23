Development
===========

Amiss' homepage is http://k3jw.com/code/amiss/

The code for Amiss is hosted at http://github.com/shabbyrobe/amiss


Running the tests
-----------------

PHPUnit Tests are in the ``tests/`` directory of the distribution. You can run the tests like so::

    cd /path/to/amiss/dist/tests/
    phpunit


There are some tests that are known to report incorrect results. These can be excluded by running
the tests like so::

    phpunit --exclude-group faulty


Building the docs
-----------------

The source distribution does not include compiled documentation, only restructuredText files. If you
need HTML or PDF documentation, it needs to be built.

If you are using OS X, the documentation build requires the following packages be available on your
machine:

* Macports
* texlive
* texlive-latex-extras
* py2x-sphinx

Linux may require a different set of packages to be installed, and if you're on Windows, might I be
so brave as to suggest VirtualBox? Or just download the pre-packaged docs from the Amiss website. I
will get around to documenting windows builds as eventually there will be a .chm file too.

Once the above dependencies are met, you can run the following commands (Linux or OS X)::

    cd /path/to/amiss/doc/
    make html
    make latexpdf


If you are already reading this in HTML or PDF format and feel the urge to smugly remark "well I'm
*already* reading it in HTML so nerny nerny nerrrrr", here is a medal::

           _______________
          |@@@@|     |####|
          |@@@@|     |####|
          |@@@@|     |####|
          \@@@@|     |####/
           \@@@|     |###/
            `@@|_____|##'
                 (O)
              .-'''''-.
            .'  * * *  `.
           :  *       *  :
          : ~ A S C I I ~ :
          : ~ A W A R D ~ :
           :  *       *  :
      jgs   `.  * * *  .'
              `-.....-' 

Thanks, "jgs". Your ASCII art fills me with gladness.

