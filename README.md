# RAML Scoop

Converts a set of RAML/JSON-Schema specifications into a single set of API documentation.

## Usage

Create a configuration file based on `./config/config.sample.php` or `./config/config.sample.json`.
There's a configuration for an example RAML specification provided with the application - 
use `example` as your config name. 

To generate a set of documentation: 

```
./bin/raml-scoop generate                               (uses [app_dir]/config/default.php)
./bin/raml-scoop generate -c example                    (uses [app_dir]/config/example.php)
./bin/raml-scoop generate -c big.json                   (uses ./big.json)
./bin/raml-scoop generate --config=/path/to/config.php  (uses /path/to/config.php)
```

To start a live preview server:

```
./bin/raml-scoop preview --config=example --port=9999
```

Then navigate to [http://localhost:9999/](http://localhost:9999/).

For comprehensive information about available options run:
```
./bin/raml-scoop
``` 

## Themes

To create a new theme take a look at the existing one in `/resources/DefaultTheme`. 
Everything in the `Assets` subdirectory gets copied over as is, and `Views` subdirectory 
needs to have a `Project.twig` file in it. It gets passed an `Project` object as `project` variable.
