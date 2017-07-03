# Configuration

SilverStripe-less doesn't have many configuration options at this time.
The two options it does have are configured using your
own YAML config file (for instance `mysite/_config/less.yml`).

```
Axllent\Less\LessCompiler:
  cache_method: 'serialize'   # (serialize / php)
  variables:
    'HeaderFont': 'Arial, sans-serif, "Times New Roman"' # note the quotes, see below!
    'HeaderFontSize': '18px'
```

## Setting cache_method

Only two caching methods are supported, `serialize` and `php`
(see [documentation](https://github.com/Asenar/less.php#caching)).


## Variables

This allows you to add your own variables which you can then use in your `*.less` stylesheets.
The above example would provide you with two variables, namely `@HeaderFont` and `@HeaderFontSize`.

```css
header h1 {
    font-family: @HeaderFont;
    font-size: @HeaderFontSize;
}
```
