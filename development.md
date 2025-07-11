# How to work with thi plugin.

## Locally

Run 
```
npm run dev
``` 

while working locally.

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