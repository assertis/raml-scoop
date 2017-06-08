# RAML Scoop

Converts a set of RAML/JSON-Schema specifications into a single set of API documentation.

## Usage

Create a configuration file based on `./config/config.sample.php`.

To generate a set of documentation: 

```
./bin/raml-scoop generate                                     (uses ./config/default.php)
./bin/raml-scoop --config=big generate                        (uses ./config/big.php)
./bin/raml-scoop --config=/full/path/to/config.php generate
```

To start a live preview server: from the same config:

```
./bin/raml-scoop --config=big --port=9999 preview
``` 

Then navigate to [http://localhost:9999/](http://localhost:9999/).

For comprehensive information about available options run:
```
./bin/raml-scoop
``` 
