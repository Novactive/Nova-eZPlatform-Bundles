# Novactive eZ Accelerator

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/Accelerator/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezaccelerator.svg?style=flat-square)](https://packagist.org/packages/novactive/ezaccelerator)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZAccelerator.svg?style=flat-square)](https://github.com/Novactive/NovaeZAccelerator/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezaccelerator.svg?style=flat-square)](LICENSE)

Accelerate your Ibexa DXP (eZ Platform)

This bundles helps and aims to accelerate:

- the overhall performances
- your developments
- productivity of your marketing/content creation teams
- your business 

eZ Accelerator leverages Symfony Messenger and adds asynchronoucity to eZ Platform allowing many things
with NO OVERHEAD and quite the opposite accelerating native actions.

For now, eZ Accelerator allows you to:

- catch and dispatch asynchronously **any Events**. (_Careful though not all can be handle asynchronously_)
- handle HTTP Cache Purge asynchronously
- handle Search Indexation asynchronously

> Bonus! You can inject Middleware into the game!

## How does that work

First you need to understand the [Symony Messenger Component](https://symfony.com/doc/current/components/messenger.html).

### Asynchronous HTTP Cache Purge

eZ Accelerator decorates the eZ Platform purger to dispatch the corresponding messages.
To enable the asynchronicity, you just need to know the Message FQDN

- **Tag Purge**: Novactive\Bundle\eZAccelerator\Message\PurgeHttpCacheTags
- **Purge All**: Novactive\Bundle\eZAccelerator\Message\PurgeAllHttpCache

> that's it! See the config example below

### Asynchronous Search Index

eZ Accelerator decorates the eZ Platform Search Handler to dispatch the corresponding messages.
To enable the asynchronicity, you just need to know the Message FQDN

- **Index Content**: Novactive\Bundle\eZAccelerator\Message\Search\IndexContent
- **Index Location**: Novactive\Bundle\eZAccelerator\Message\Search\IndexLocation
- **Remove Content**: Novactive\Bundle\eZAccelerator\Message\Search\UnindexContent
- **Remove Location**: Novactive\Bundle\eZAccelerator\Message\Search\UnindexLocation
- **Purge All**: Novactive\Bundle\eZAccelerator\Message\Search\PurgeIndex

> that's it! See the config example below

### Asynchronous Event handling

The concept is simple, everytime eZ Platform is doing something an event is triggered. eZ Accelerator gives you the 
opportunity to handle the event through a bus which gives you the opportunity to handle that event via a message 
synchronously or asynchronicity using the transport of your choice.

Everything is opt-in, you can still use the default event dispatcher or you can switch to the bus approach.

## Configuration example

### Handle the Event: eZ\Publish\API\Repository\Events\Bookmark\CreateBookmarkEvent through a bus SYNCHRONOUSLY

```yaml
nova_ezaccelerator:
    system:
        default:
            # default_bus: a.default.bus.for.this.siteaccess.config
            event_to_message:
                eZ\Publish\API\Repository\Events\Bookmark\CreateBookmarkEvent:
                    message: Novactive\Bundle\eZAccelerator\Message\VoidEventMessage # should be your own
                    # stop_propagation: false # default
                    # bus: a.specific.bus.for.this.siteaccess.config.and.that.event
```

> You can decide to stop the event propagation as well if it makes sense.

### Transport Configuration

```yaml
framework:
    messenger:
        buses:
            my.bus: # could be anything and you can have many
                middleware:
                    - Novactive\Bundle\eZAccelerator\Core\SiteAccessAwareMiddleware

        transports:
            ezaccelerator: 'doctrine://default?queue_name=nova_ezaccelerator' # you decide the name does not matter

        routing:
            Novactive\Bundle\eZAccelerator\Message\VoidEventMessage: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeAllHttpCache: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeHttpCacheTags: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\IndexContent: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\IndexLocation: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\UnindexContent: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\UnindexLocation: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\PurgeIndex: ezaccelerator
```

With this configuration you can handle **asynchronously** whatever you want when you want.

> Again, careful when all the AfterEvent can probably all be handle asynchronously the main work has to be synchronous
> for most of the case. (Ex: Content Creation, etc.)

## Considerations

### Consumers and SiteAccesses in complex MultiSiteAccess situation

When running the consumers, you will probably run them via **1** siteaccess (`default` maybe).

Thing is you might want to know the original SiteAccess. That's why eZ Accelerator injects the `SiteAccessAwareMiddleware`
which tags the Message with the Orginal SiteAccess.

Nevertheless, and even more importantly, in your handler you need to handle according to the correct configuration.

Let's imagine a situation where you have 2 siteaccess:

- **A**: where the Varnish server is `V.IP.A` and database `DB.A` (default)
- **B**: where the Varnish server is `V.IP.B` and database `DB.B`

When you run your consumer, it will use `default` siteaccess which is setup for Varnish host `V.IP.A`
A message from the siteaccess `B` is dispatched, it will be handle by the only consumer you ran connected in-memory to siteaccess `A`
Therefore in this situation `V.IP.B` won't never be purged.


To handle those situations you can run and setup different consumers and transports and play with eZ Accelerator / Messsenger config.

Ex: 

- All default `config bin/console messenger:consume`
- SiteAccess `A` `bin/console messenger:consume --siteaccess=A`

But you can also specify the `bus` and the `receiver`

- `bin/console messenger:consume ezaccelerator --siteaccess=A --bus=something`


## Features

[Implemented](documentation/FEATURES.md)

## Installation instructions

[Installation](documentation/INSTALL.md)

## Changelog 

[Changelog](documentation/CHANGELOG.md)

