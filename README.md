Basket (module for Omeka S) [archived]
===========================

[Basket] is a module for [Omeka S] that allows an anonymous visitor or a user to
select resources and to save them in a basket instantly, and to keep them
through sessions.

**IMPORTANT**
This fork of the module [Basket of BibLibre] is deprecated and replaced by the
module [Selection] in order to manage improvements.


Installation
------------

Install the optional modules [Generic] and [Guest] if wanted.

Uncompress files in the module directory and rename module folder `Basket`.
Then install it like any other Omeka module and follow the config instructions.

See general end user documentation for [Installing a module].


Usage
-----

The user can see a basket in the item page. On a click, the item is added to the
basket, or removed. The full list of resources in the basket is available at
"/s/my-site/guest/basket". This page is available for guest users, but for any
other registered user too.

It is recommended to edit the theme directly to include the basket besides the
item and the media, in particular in the item/show and the item/browse views.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitHub.


License
-------

This module is published under the [CeCILL v2.1] licence, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.


Copyright
---------

* Copyright Biblibre, 2016-2017 (see [Biblibre])
* Copyright Daniel Berthereau, 2017-2020 (see [Daniel-KM] on GitHub)


[Basket]: https://github.com/Daniel-KM/Omeka-S-module-Basket
[Omeka S]: https://omeka.org/s
[Generic]: https://github.com/Daniel-KM/Omeka-S-module-Generic
[Guest]: https://github.com/Daniel-KM/Omeka-S-module-Guest
[Basket of BibLibre]: https://github.com/BibLibre/Omeka-S-module-Basket
[Selection]: https://github.com/Daniel-KM/Omeka-S-module-Selection
[Installing a module]: http://dev.omeka.org/docs/s/user-manual/modules/#installing-modules
[module issues]: https://github.com/Daniel-KM/Omeka-S-module-Basket/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[Biblibre]: https://github.com/biblibre
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
