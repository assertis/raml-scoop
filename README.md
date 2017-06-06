# RAML Scoop

Converts a set of RAML/JSON-Schema specifications into a single set of API documentation.

## Usage

Create a configuration file based on `./config/config.sample.php`, for example `./config/big.php`.

To generate a set of documentation from `./config/big.php` (or `.json`): 

```
./bin/raml-scoop --config=big generate
``` 

To start a live preview server: from the same config:

```
./bin/raml-scoop --config=big --port=9999 preview
``` 

Then navigate to [http://localhost:9999/](http://localhost:9999/).
