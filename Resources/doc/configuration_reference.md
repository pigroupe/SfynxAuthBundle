#Configuration Reference

All available configuration options are listed below with their default values.

``` yml
#
# SfynxAuthBundle configuration
#  
sfynx_auth:
    firewall_name: main
    mapping:
        entities:
            user:
                class: Sfynx\AuthBundle\Domain\Entity\User
                provider_command: 'orm'
                provider_query: 'orm'
                em_command: doctrine.orm.entity_manager
                em_query: doctrine.orm.entity_manager
                repository_command: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Command\Orm\UserRepository
                repository_query: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Query\Orm\UserRepository
            group:
                class: Sfynx\AuthBundle\Domain\Entity\Group
                provider_command: 'orm'
                provider_query: 'orm'
                em_command: doctrine.orm.entity_manager
                em_query: doctrine.orm.entity_manager
                repository_command: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Command\Orm\GroupRepository
                repository_query: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Query\Orm\GroupRepository
            langue:
                class: Sfynx\AuthBundle\Domain\Entity\Langue
                provider_command: 'orm'
                provider_query: 'orm'
                em_command: doctrine.orm.entity_manager
                em_query: doctrine.orm.entity_manager
                repository_command: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Command\Orm\LangueRepository
                repository_query: Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Query\Orm\LangueRepository
    loginfailure:
        authorized: true
        time_expire: 3600
        connection_attempts: 5
        cache_dir: "%sfynx_cache_dir%/loginfailure/"
    locale:
        authorized: ~ #[fr_FR, en_GB]
        cache_file: "%sfynx_cache_dir%/languages.json"
    browser:
        switch_language_authorized: true
        switch_layout_mobile_authorized: false
    default_layout:
        init_pc:
            template: layout-pi-sfynx.html.twig
        init_mobile:
            template: Default
    default_login_redirection:
        redirection: admin_homepage
        template: layout-pi-admin-cmf.html.twig
```
