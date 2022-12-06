# Thirst Moodle Plugin
  1. [Development](#development)
  2. [Release plugin](#release-plugin)

## Development
In order to develop the plugin, it must be placed inside `public/local/thirst` as a submodule.
```bash
git submodule add git@github:your-fork/Thirst_Moodle public/local/thirst
```

### Configuration
Once added, prepare configuration of the plugin.
```bash
cd public/local/thirst
cp config.example.json config.json
```
Inside configuration, you will notice that `api.endpoint` contains `%s` - this is a placeholder for the custom subdomain name.

Next, make sure that all dependencies are installed
```bash
cd public/local/thirst
composer install
```

## Release plugin
Make sure that the configuration of the plugin inside `config.json` is correct. Once confirmed, perform following steps from the repository root
```bash
# Build an image that will be used to create a release
# This will give you correct environment as well as install all dependencies
docker-compose up --build -d

# Once built, create a release
docker-compose exec php php bin/release.php

# You should see the output of what is happening. At the end of the script you should see following message
Completed! Package created thirst-lms-plugin-2019120201.zip
```