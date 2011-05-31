
# Statiker

Statiker is a MODx Revolution Extra that generates static files from resources.

## System Settings / Context Settings

  * friendly_urls - Must be TRUE
  * site_url - Must be defined.
  * statiker.farm_url

## Template Variables

  * statiker.build
  * statiker.compress
  * statiker.gzencode
  * statiker.overwrite

## Development Setup

### Namespace

statiker
[...]/modx/mycomps/statiker/core/components/statiker/

### System Settings

statiker.assets_url
/modx/mycomps/statiker/assets/components/statiker/

statiker.core_path
[...]/modx/mycomps/statiker/core/components/statiker/

### Actions

Controller: index
Namespace: statiker
Language Topics: statiker:default

### Top Menu (within Actions)

Lexicon Key: statiker
Description: statiker.desc
Action: statiker - index
