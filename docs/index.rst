Memento for Wordpress
=====================

.. image:: _static/plugin-logo.png
    :alt: Memento for Wordpress logo

A plugin for Wordpress web sites to enable the Memento framework for time-based access

What is Memento?
----------------

Memento is a `HTTP-based framework <http://mementoweb.org/guide/rfc/>`_
that "bridges the past and present web." Sites that support it — like the
Internet Archive and the Library of Congress — accept time-based web requests
and return past versions of archived URLs.

What is Wordpress?
------------------

Wordpress is a `free and open-source content-management system <https://en.wikipedia.org/wiki/WordPress>`_
commonly used by online publishers to create blogs. Its modular architecture allows outside
developers to extend its features by developing "plugins" for users to install.

What does this plugin do?
-------------------------

Rather than rely on patchy third-party services for preservation, sites with
this plugin installed instantly become their own living, digital archive.

Past revisions of posts, inaccessible to users of a typical Wordpress site,
are indexed and available for review.

Once installed, this package will instantly upgrade a Wordpress site to
support `the Memento system <http://www.mementoweb.org/guide/quick-intro/>`_ by:

* Creating a new URL for past revisions of each post stored in the Wordpress database, so archived versions are surfaced online
* Publishing a `"TimeMap" <http://mementoweb.org/guide/rfc/#Pattern6>`_ that lists all of the revision URLs for each post in your archive, so spiders can find and index archived revisions
* Hosting a `"TimeGate" <http://mementoweb.org/guide/rfc/#component-1.2>`_ that handles requests that include a URL and a timestamp by redirecting to the nearest revision, so browsers and other apps can "time travel" on your site
* Enriching post and revision URLs to include extra metadata required by the Memento system, so archived pages can be connected to each other

How do I try it?
----------------

Download the source code from `the GitHub repository <https://github.com/pastpages/wordpress-memento-plugin>`_
and install it manually on your Wordpress site using `the recommended method <https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation>`_.

This software is a beta release and in active development. Please report issues,
share your thoughts and contribute code patches `via GitHub <https://github.com/pastpages/wordpress-memento-plugin/issues>`_.

Then what happens?
------------------

Once you have the plugin installed, your site will instantly support Memento.

To see it in action, find the URL of a post on your site that has been revised several times
after its initial publication. For this example, let's assume you have a post published at the following URL:

.. code:: bash

    http://myblog.com/2015/08/17/hello-world/

One way to find past revisions is to browse the "TimeMap" that lists them in a machine-readable format.
To do that, submit the post's URL to a new TimeMap URL activated by plugin.

.. code:: bash

    http://myblog.com/timemap/http://myblog.com/2015/08/17/hello-world/

That should return a `link-format <http://tools.ietf.org/html/rfc5988>`_ list with URLs that point to past revisions.

.. code:: xml
    TimeMap example code goes here.

The revision URL should look similar to the post you selected, but include the unique identifier number of
each revision at the end, like this:

.. code:: bash

    http://myblog.com/2015/08/17/hello-world/?revision=7

Visit one of the URLs in the list and you will see the new revision page added by the plugin.
The page will look identical to the standard post page, but feature the content from the archived
version of the post instead.

.. code:: bash

    Picture of revision post page now

Another way to navigate to a revision page is via the "TimeGate" redirection service.
Requests that submit a post URL with a timestamp in the ``Accept-Datetime`` header will be automatically
rerouted to nearest revision.

You can see it in action by submitting a request from the command line via curl:

.. code:: bash

    $ curl -X GET -I http://myblog.com/timegate/http://myblog.com/2015/08/17/hello-world/ --header "Accept-Datetime: Mon, 27 July 2015 01:00:00 GMT"

Which returns a 302 redirect that looks like this:

.. code:: bash

    curl response stdout here
