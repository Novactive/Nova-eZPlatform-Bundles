# Novactive eZ Accelerator

# Install

## Installation steps

Add the lib to your composer.json, run `composer require novactive/ezaccelerator` to refresh dependencies.

## Configuration

First you need to instruct Messenger

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        buses:
            my.bus:
                middleware:
                    - Novactive\Bundle\eZAccelerator\Core\SiteAccessAwareMiddleware
        
        # simplest one here with doctrine, but you can use Rabbit MQ or any other 
        transports:
            ezaccelerator: 'doctrine://default?queue_name=nova_ezaccelerator'

        routing:
            Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeAllHttpCache: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeHttpCacheTags: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\IndexContent: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\IndexLocation: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\UnindexContent: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\UnindexLocation: ezaccelerator
            Novactive\Bundle\eZAccelerator\Message\Search\PurgeIndex: ezaccelerator

            # Your own
            # Novactive\Bundle\eZAccelerator\Message\VoidEventMessage: ezaccelerator

```

Then eZ Accelerator

```yaml
# config/packages/ezaccelerator.yaml

nova_ezaccelerator:
    system:
        default:
            # # default_bus: a.default.bus.for.this.siteaccess.config
            # event_to_message:
            #    eZ\Publish\API\Repository\Events\Bookmark\CreateBookmarkEvent:
            #        message: Novactive\Bundle\eZAccelerator\Message\VoidEventMessage
            #        stop_propagation: false # default
            #        # bus: a.specific.bus.for.this.siteaccess.config.and.that.event

```

### Register the bundle

If Symfony Flex did not do it already, activate the bundle in `config\bundles.php` file.

```php
// config\bundles.php
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    ...
    Novactive\Bundle\eZAccelerator\NovaeZAccelerator::class => ['all' => true],
];
```
