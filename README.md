# Laravel Media Uploader
<p align="center">
<a href="https://github.com/ahmed-aliraqi/laravel-media-uploader/actions"><img src="https://github.com/ahmed-aliraqi/laravel-media-uploader/workflows/tests/badge.svg" alt="Build Status"></a>
	<a href="https://github.styleci.io/repos/275045511"><img src="https://github.styleci.io/repos/275045511/shield?style=flat" alt="StyleCI"></a>
	<a href="https://travis-ci.org/ahmed-aliraqi/laravel-media-uploader"><img src="https://travis-ci.org/ahmed-aliraqi/laravel-media-uploader.svg?branch=master" alt="Travis Build Status"></a>
	<a href="https://packagist.org/packages/ahmed-aliraqi/laravel-media-uploader"><img src="https://poser.pugx.org/ahmed-aliraqi/laravel-media-uploader/d/total.svg" alt="Total Downloads"></a>
	<a href="https://packagist.org/packages/ahmed-aliraqi/laravel-media-uploader"><img src="https://poser.pugx.org/ahmed-aliraqi/laravel-media-uploader/v/stable.svg" alt="Latest Stable Version"></a>
	<a href="https://packagist.org/packages/ahmed-aliraqi/laravel-media-uploader"><img src="https://poser.pugx.org/ahmed-aliraqi/laravel-media-uploader/license.svg" alt="License"></a>
</p>

> This package used to upload files using laravel-media-library before saving model.

![Uploader](https://github.com/ahmed-aliraqi/laravel-file-uploader/blob/master/screenshots/uploader-v2.gif?raw=true)

> In this package all uploaded media will be processed.
* All videos will converted to `mp4`.
* All audios will converted to `mp3`.
* All images `width` & `height` & `ratio` will be saved as custom property. 
* All videos & audios `duration` will be saved as custom property. 
#### Requirements
- PHP >= 7.4
- You should be ensured that the [ffmpeg](https://ffmpeg.org) was installed on your server

#### Installation
```bash
composer require ahmed-aliraqi/laravel-media-uploader
```
> The package will automatically register a service provider.
  
> You need to publish and run the migration:

```bash
php artisan vendor:publish --provider="AhmedAliraqi\LaravelMediaUploader\Providers\UploaderServiceProvider" --tag="migrations"

php artisan migrate
```
> Publish [laravel-media-library](https://github.com/spatie/laravel-medialibrary) migrations:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
```

> If you want to customize `attachments` validation rules, you should publish the config file:

```bash
php artisan vendor:publish --provider="AhmedAliraqi\LaravelMediaUploader\Providers\UploaderServiceProvider" --tag="config"
```

> If you want to customize validation translations, you should publish the `lang` files:

```bash
php artisan vendor:publish --provider="AhmedAliraqi\LaravelMediaUploader\Providers\UploaderServiceProvider" --tag="uploader:translations"
```

> This is the default content of the config file:

```php
<?php

return [
    /*
     * Regenerate uploaded media after assign to model
     */
    'regenerate-after-assigning' => true,

    'documents_mime_types' => [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .doc & .docx
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .ppt & .pptx
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xls & .xlsx
        'text/plain',
        'application/pdf',
        'application/zip',
        'application/x-rar',
        'application/x-rar-compressed',
        'application/octet-stream',
    ],
];
```

> Use `HasUploader` trait in your model:

```php
<?php

namespace App;


use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;

class Blog extends Model implements HasMedia
{
    use InteractsWithMedia, HasUploader;
    ...
}
```
> In your controller use `addAllMediaFromTokens()` method to assign the uploaded media to model using the generated tokens:

```php
class BlogController extends Controller
{
        public function store(Request $request)
        {
            $blog = Blog::create($request->all());
            
            $blog->addAllMediaFromTokens();
    
            return back();
        }
}
``` 
> If you do not add any arguments in `addAllMediaFromTokens()` method will add all tokens in `request('media')` with any collection.
>
>If you want to save specific collection name add it to the second argument.
```php
// specified collection name
$blog->addAllMediaFromTokens([], 'pictures');

// specified tokens
$blog->addAllMediaFromTokens($request->input('tokens', []), 'pictures');
```

#### Front End Basic Usage
```blade
<div id="app">
    <file-uploader
            :unlimited="true"
            collection="avatars"
            :tokens="{{ json_encode(old('media', [])) }}"
            label="Upload Avatar"
            notes="Supported types: jpeg, png,jpg,gif"
            accept="image/jpeg,image/png,image/jpg,image/gif"
    ></file-uploader>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-file-uploader"></script>
<script>
  new Vue({
    el: '#app'
  })
</script>
```
###### Or Install Component Via NPM

```bash
npm i laravel-file-uploader --save-dev
``` 
> Now you should register the component in your `resources/js/app.js`:

```js
// app.js

import FileUploader from 'laravel-file-uploader';

Vue.use(FileUploader);
```

#### Usage
```blade
<file-uploader :media="{{ $user->getMediaResource('avatars') }}"
               :unlimited="true"
               collection="avatars"
               :tokens="{{ json_encode(old('media', [])) }}"
               label="Upload Avatar"
               notes="Supported types: jpeg, png,jpg,gif"
               accept="image/jpeg,image/png,image/jpg,image/gif"
></file-uploader>
```
##### Attributes
| Attribute |Rule | Type  |Description |
|--|--|--|--|
| media | optional - default: `[]`  |array | used to display an existing files  |
| unlimited |optional - default:`false`| boolean| upload unlimited files - if let it `false` will not be multiple select|
| max|optional - default:`12`| int| the maximum uploaded files - if `1` will not me multiple select|
|accept| optional - default: `*`| string| the accepted mime types|
|form| optional - default: `false`| string| the form id of the uploaded media|
|notes| optional - default `null`| string| the help-block that will be displayed under the files|
|label| optional - default `null`| string| the label of the uploader|
|collection| optional - default `default`|string| the media library collection that the file will store in|
|tokens| optional - default: `[]`|array|the recently uploaded files tokens, used to display recently uploaded files in validation case|

#### Using with BsForm
> This uploader support [laravel-bootstrap-forms](https://github.com/laraeast/laravel-bootstrap-forms) you can use the `image` custom component instead of vue html tag:

```blade
{{ BsForm::image('avatar')->collection('avatars')->files($user->getMediaResource('avatars')) }}
{{ BsForm::audio('audio')->collection('audios')->files($user->getMediaResource('audios')) }}
{{ BsForm::video('video')->collection('videos')->files($user->getMediaResource('videos')) }}
{{ BsForm::media('media')->collection('videos')->accept('video/*')->files($user->getMediaResource('videos')) }}
```
```blade
{{ BsForm::image('avatar')->max(3)->collection('avatars')->files($user->getMediaResource('avatars')) }}
```
```blade
{{ BsForm::image('image')->unlimited()->files($user->getMediaResource()) }}
```
```blade
{{ BsForm::image('avatar')->collection('avatars') }}
```

> Note: do not forget to add Cron job in your server to remove the expired temporary media.

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
> **Note:**
> Do not forget to store the `csrf` token in an HTML `meta` tag:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

#### API
* Upload Files
    * endpoint: /api/uploader/media/upload
    * method: POST
    * body: 
        * files[]: multipart form data
    * response:
        * ![upload response](https://i.imgur.com/dvPX9Wa.png)
* Display Recently Uploaded Files
    * endpoint: /api/uploader/media
    * method: GET
    * params:
        * tokens[]: temporary token
    * response:
        * ![response](https://i.imgur.com/0xaaDPK.png)
* Delete Files
    * endpoint: /api/uploader/media/{id}
    * method: DELETE
    * response:
        * ![response](https://i.imgur.com/dghxe47.png)
