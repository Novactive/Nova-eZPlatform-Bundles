# Novactive eZ Mailing Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/MailingBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezmailingbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezmailingbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZMailingBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZMailingBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezmailingbundle.svg?style=flat-square)](LICENSE)

Send eZ Contents by email based on the Mailing + Campaign configuration to a group of users (Mailing Lists)

Wording
-------

- Campaign: 
    - A container of Mailing
    - With common metadata like
        - Sender (email and name)
        - Report email
    - Limited by Siteaccess
    - Related to Mailing Lists
    - Associated with Content 
    
- Mailing:
    - A Content to be send
    - A sending date or a frequency

- Mailing List:
    - A container of Users
   
- User:
    - Hold the data of a User (not a eZ User)
    
- Registration:
    - An association of a User and a Mailing List
    
- Broadcast:
    - A record of a Mailing being sent to the Users of the Mailing Lists related to its Campaign
    - Containing the metadata (date, number of recipients etc.)

- A StatHit:
    - A record that tracks
        - email openings
        - click on URLs
                       
Features
--------

Novactive eZ Mailing allows you to: **Send eZ Contents by email based on the Mailing + Campaign configuration to a group
of users (Mailing Lists)** 

At a glance:

- Registration management
    - user information
    - registration and unregistration form (with confirmation email)
    - registration approbation (optional on a Mailing List)

- eZ Platform 2.x Admin UI
    
- Campaign management (CRUD)
    - Mailing (newsletter) management (CRUD)
        - one shot
        - recurrence
    - Mailing List management (CRUD)
    - Statistics and charts (open emails, clicks etc.)
    - Test a Mailing
    
- Users
    - Import into Mailing List
    - Export
    
- Multilingual

- SiteAccess Aware (and SiteAccess Limitations)

- Tracking

- Personnalization

- A mailer for the Mailings

- A mailer for the Service Emails


Mailing Workflow
----------------

Here is the possible status changements of a Mailing. 

![Mailing Status Workflow ](bundle/Resources/doc/images/mailing_workflow.png)

Usage and installation instructions
-----------------------------------

[Installation](bundle/Resources/doc/INSTALL.md)

[Usage](bundle/Resources/doc/USAGE.md)


Change
------

[Changelog](bundle/Resources/doc/CHANGELOG.md)


Special Mentions and Credits
----------------------------

- This bundle features are inspired by https://github.com/cjw-network/cjw_newsletter fusionned with eZ Mailing
(and old Novactive bundle never released in Open Source)
