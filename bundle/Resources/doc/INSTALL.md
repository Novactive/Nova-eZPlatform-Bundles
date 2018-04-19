# Installation


## Requirements

* eZ Platform 2+
* PHP 7.1+
* MySQL 5.7.8+ / Maria DB 10.2.4+

## Installation steps


    
_novaezmailing_routes:
    resource: '@NovaeZMailingBundle/Resources/config/routing.yml'


// Nova eZ Mailing
new Novactive\Bundle\eZMailingBundle\NovaeZMailingBundle()



nova_ezmailing:
    system:
        default:
            simple_mailer: "swiftmailer.mailer.myfirst_mailer"
            mailing_mailer: "swiftmailer.mailer.mysecond_mailer"


ezpublish:
    system:
        default:
            content_view:
                novaezmailingfull:
                    folder:
                        template: yourtemplatepath
                        match:
                            Identifier\ContentType: [a_content_type]
