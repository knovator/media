This package used for upload all type media file 

Migration Added:

- Files

Features:
- Upload media file
- Change image size

Require the knovator/media package in your composer.json and update your dependencies:

You want to need add media repository in your composer.json file.

```"repositories": [
          {
              "type": "vcs",
              "url": "git@github.com:knovator/media.git"
          }
      ],
```

This package included 
```prettus/l5-repository``` and ```knovator/support``` and ```knovator/image-resize``` packages.
```
    composer require knovator/media "1.*"
 ```
In ```knovator/image-resize``` include ```illuminate/support``` and ```intervention/image```

Intervention Image is a PHP image handling and manipulation library providing an easier and expressive way to create, edit, and compose images.

In your ```config/app.php``` add ```Knovator\Media\MediaServiceProvider::class``` to the end of the providers array:

Publish Configuration:

```php artisan vendor:publish --provider "Knovator\Media\MediaServiceProvider"```

website : [ https://github.com/knovator/media ]
