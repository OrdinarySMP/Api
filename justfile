dev:
    zellij --layout dev.kdl

check:
    ./vendor/bin/pint
    ./vendor/bin/phpstan analyse --memory-limit 2G

ide:
    php artisan ide-helper:generate
    php artisan ide-helper:eloquent
    php artisan ide-helper:models -W

types:
    php artisan typescript:transform

alias c := check
alias t := types
