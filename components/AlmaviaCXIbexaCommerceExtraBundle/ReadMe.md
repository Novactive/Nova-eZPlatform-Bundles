
# Add some facilicities to ibexa commerce

- Siteaccess Aware Cart
  Avec l'ajout du `DatabaseHandler Decorator` `SessionHandler decorator`, nous donnons la possibilité d'ajouter d'avoir un panier par siteaccess (le nom du panier est configurable par siteaccess)
## Installation

Pour installer ce bundle, utilisez Composer :
### pré-requis
Il faut avoir installé ibexa/commerce
### Repositories:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/Novactive/AlmaviaCXIbexaCommerceExtraBundle.git" },
      {"type": "composer", "url": "https://updates.ibexa.co"}
    ]
}
```
```bash
composer require almaviacx/ibexa-commerce-extra-bundle  
```

## Configuration

Ajoutez le bundle à votre fichier `config/bundles.php` :

```php
return [
    // ...
    AlmaviaCX\Ibexa\Commerce\Extra\AlmaviaCXIbexaCommerceExtraBundle::class => ['all' => true],
];
```

## Utilisation

### activer le mode Named Cart (siteaccess aware) example

```yaml
almaviacx_ibexa_commerce_extra:
    default: # siteaccess 
      named_cart_enabled: true
    atlantic_pro:
      named_cart_name: 'atlantic_pro_cart'
      named_workflow_name: ibexa_checkout
    atlantic_boutique:
      named_cart_name: 'default'
      named_workflow_name: ibexa_checkout
```
## Dépendances

Ce bundle nécessite les dépendances suivantes :

- PHP : dépendance Ibexa 4.6
- Ibexa Commerce : ^4.6

## Auteurs Almavia CX

- Almaviacx <dir.tech@almaviacx.com>
- Ousmane KANTE <ousmane.kante@almaviacx.com>`