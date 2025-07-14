# How to work with thi plugin.

## Locally

- Copy source code to `/wp-content/plugins/dt-home/` on local site.
- Within the site plugin directory (above):
  - Run `composer install`
  - Run `npm install`
  - Run `npm run dev`

Note: If vendor-scoped folder is not generated or `composer install` always says php-scoped needs to be installed, make sure the [Composer bin folder is in your PATH](https://stackoverflow.com/a/64545124/92876).

## Packaging for production

The CI will create a package for production. If you want to do it locally to test:

```
#remove the vendor folders
rm -rf vendor
rm -rf vendor-scoped

#install the prod vendor scripts
composer install --no-dev --no-interaction --ignore-platform-reqs

#build the dependencies
npm run build

#create the plugin zip
mkdir dt-home
cp -r dt-home.php config dist languages resources routes src vendor vendor-scoped composer.json composer.lock composer.scoped.json composer.scoped.lock LICENSE package.json package-lock.json dt-home/
zip -r dt-home.zip dt-home
rm -rf dt-home
