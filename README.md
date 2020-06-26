# Laravel Media Uploader

> This package used to upload files using laravel-media-library before saving model.

#### Installation
```bash
composer require ahmed-aliraqi/laravel-media-uploader
```
> after install the package you should publish the uploader icons using the following command:

```bash
php artisan uploader:install
```
> Use `HasUploader` trait in your model:

```php
<?php

namespace App;


use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use AhmedAliraqi\LaravelMediaUploader\Entities\Concerns\HasUploader;

class Blog extends Model implements HasMedia
{
    use HasMediaTrait, HasUploader;
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

> Now you should install the uploader vue component using `npm`

```bash
npm i laravel-file-uploader --save-dev
``` 
> Now you should register the component in your `resources/app.js`:

```js
// app.js

import FileUploader from 'laravel-file-uploader';

Vue.use(FileUploader);
```

#### Usage
```blade
<file-uploader :media="{{ $user->getMediaResource('avatars') }}"
               :max="1"
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
| max|optional - default:`12`| int| the maximum uploaded files - if `1` will not me multiple select|
|accept| optional - default: `*`| string| the accepted mime types|
|notes| optional - default `null`| string| the help-block that will be displayed under the files|
|label| optional - default `null`| string| the label of the uploader|
|collection| optional - default `default`|string| the media library collection that the file will store in|
|tokens| optional - default: `[]`|array|the recently uploaded files tokens, used to display recently uploaded files in validation case|

#### Using with BsForm
> This uploader support [laravel-bootstrap-forms](https://github.com/Elnooronline/laravel-bootstrap-forms) you can use the `image` custom component instead of vue html tag:

```blade
{{ BsForm::image('avatar')->collection('avatars')->files($user->getMediaResource('avatars')) }}
```
```blade
{{ BsForm::image('avatar')->max(3)->collection('avatars')->files($user->getMediaResource('avatars')) }}
```
```blade
{{ BsForm::image('avatar')->collection('avatars') }}
```