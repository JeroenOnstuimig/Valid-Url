# Valid-Url

Makes sure that a URL has a protocol, that ampersands are converted to entities, and all other characters are properly URL encoded.

## Usage

### `{exp:valid_url}`

#### Example Usage

```
{exp:valid_url}www.example.com/foo bar/bat?=bag&mice=men!{/exp:valid_url}

```

Produces:

```
http://www.example.com/foo+bar/bat?=bag&amp;mice=men%21
```

## Change Log

### 2.1 - Onstuimig

- Added mailto, relative URL and hash fragment support

### 2.0

- Updated plugin to be 3.0 compatible

### 1.1

- Updated plugin to be 2.0 compatible
