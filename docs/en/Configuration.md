# Configuration

SilverStripe-less doesn't have many configuration options at this time.
The two options it does have are configured using your
own YAML config file (eg: `app/_config/less.yml`).

```yaml
Axllent\Less\LessCompiler:
  variables:
    HeaderFont: 'Arial, sans-serif, "Times New Roman"' # note the quotes, see below!
    HeaderFontSize: 18px
```

## Variables

This allows you to add your own variables which you can then use in your `*.less` stylesheets.
The above example would provide you with two variables, namely `@HeaderFont` and `@HeaderFontSize`.

```css
header h1 {
    font-family: @HeaderFont;
    font-size: @HeaderFontSize;
}
```

## Editor CSS

The module will automatically add any editor.css file (used on the front-end) to TinyMCE provided it is compiled as an individual file (ie: exists in your `assets/_css/*-editor.css`).
