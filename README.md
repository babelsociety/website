Welcome to Babel Society website!


# Contribute

All contribution are welcomed, feel free to open a Pull Request if you want to improve writings or publish a new post.

## Content

We expect most contributions to be on the content level.

This is a static website powered by [Zola](https://www.getzola.org/), but to contribute all you need to know is [Markdown](https://www.markdownguide.org/).

All pages are in `content` directory, once you applied the changes you can have a preview by running:
```sh
zola serve
```

Getting `zola` is pretty easy, but we recommend to install [nix](https://nixos.org/explore.html) and then simply run `nix-shell`.


## Translations

Zola supports multi-language content, it would be awesome if you could contribute by translating content in your native language. That's the main reason why all pages are in the form `page-name/index.md`.

To add a new language we need some scaffolding though, so open an Issue if you are interested and we will work on it together.

Once the language is already supported, you can created a new file with the appropriate language extension. For example, suppose we would like to translate the Manifest in Italian. The path for the Manifest in the default language is:

```
content/manifest/index.md
```

The Italian one will then be:

```
content/manifest/index.it.md
```

Notice that we have added the language code `.it` just before the `.md` extension.
