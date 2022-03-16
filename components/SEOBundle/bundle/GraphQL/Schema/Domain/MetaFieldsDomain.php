<?php

namespace Novactive\Bundle\eZSEOBundle\GraphQL\Schema\Domain;

use Generator;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformGraphQL\Schema;
use EzSystems\EzPlatformGraphQL\Schema\Builder;
use EzSystems\EzPlatformGraphQL\Schema\Domain;
use Novactive\Bundle\eZSEOBundle\GraphQL\Helper\NameHelper;

class MetaFieldsDomain implements Domain\Iterator, Schema\Worker
{
    const TYPE = 'NovaSeoMetasFieldValue';
    const ARG = 'MetaField';

    /**
     * @var ConfigResolverInterface
     */
    private ConfigResolverInterface $configResolver;

    private NameHelper $nameHelper;


    public function __construct(ConfigResolverInterface $configResolver, NameHelper $nameHelper)
    {
        $this->configResolver = $configResolver;
        $this->nameHelper = $nameHelper;
    }

    public function iterate(): Generator
    {
        $metasConfig = $this->configResolver->getParameter('fieldtype_metas', 'nova_ezseo');
        foreach (array_keys($metasConfig) as $metaName) {
            yield [self::ARG => $this->nameHelper->sanitizeMetaFieldName($metaName)];
        }
    }

    public function init(Builder $schema)
    {
        $schema->addType(new Builder\Input\Type(
            self::TYPE,
            'object'
        ));
    }

    public function work(Builder $schema, array $args)
    {
        $schema->addFieldToType(
            self::TYPE,
            new Builder\Input\Field($args[self::ARG], "String")
        );
    }

    public function canWork(Builder $schema, array $args)
    {
        return array_key_exists(self::ARG, $args);
    }

    public function setNameHelper(NameHelper $nameHelper)
    {
        $this->nameHelper = $nameHelper;
    }

    protected function getNameHelper()
    {
        return $this->nameHelper;
    }
}