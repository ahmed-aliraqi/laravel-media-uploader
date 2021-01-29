# Release Notes for Laravel Media Uploader
### v5.1.0
* **Added**
  - Add `AudioComponent` for `laravel-bootstrap-forms`,
  - Add `VideoComponent` for `laravel-bootstrap-forms`,
  - Add `Audioomponent` for `laravel-bootstrap-forms`,
  - Add `Mediaomponent` for `laravel-bootstrap-forms`,
  - Add `_ide_helper.php` file to provide autocomplete information to your IDE.

### v5.0.1
* **Fixes**
    - Keep only configured latest media [4e3f6e6](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/4e3f6e6c4b25797fafa1cae3173e89a93e260339).
### v5.0.0
* **Added**
    - Add `form` and `unlimited` option to form component.
### v4.1.1
* **Fixes**
    - Fix support for laravel 8.x [678f06d](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/678f06d8441c2cbd8923bc3f0c6aa7b831c36f78)
### v4.1.0
* **Added**
    - Add support for laravel 8.x
### v4.0.0
* **Changes**
    - Upgraded media-library to ^8.0. [c7f1e8e](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/c7f1e8eda602d4b377cb33c98cf244c200dd1cf1)
### v3.0.0
* **Changes**
    - change vendor name of laravel-bootstrap-forms. [d09599d](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/d09599d07d8e6ca92f393de0dd0a47cc1c934b32)
### v2.1.0
* **Added**
    - Add "regenerate-after-assigning" option in config file [4fb569b](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/4fb569ba99dafd3098698e4aa274c1868d0d9206)
* **Changes**
    - The first argument of `addAllMediaFromTokens($tokens)` now support `string`, `array`, `null` value. [a451ebb](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/a451ebbdfac6e94ca1c588977a4ada4c489a48bf)
### v2.0.1
* **Fixes**
    - Register and publish translations [b5b7dd3](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/b5b7dd3efd11a6c0c6aeac82e83003da645a1a09)
### v2.0.0
* **Changes**
    - Remove built in migration and use published instead [8611ac6](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/8611ac6bbb9b8833c8231ae8d03e4cf1cb7d6866).
    - Remove `uploader:install` command line [7f0bb58](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/7f0bb58b45f634ba4937ff7cdfee025e8a6e021b).
    - Optional `preview` flag in MediaResource [e16344d](https://github.com/ahmed-aliraqi/laravel-media-uploader/commit/e16344de7eed1fdd33c33186fc4c0b21df23f835).
### v1.0.1
* **Changes**
    - Add tow optional arguments in `addAllMediaFromTokens()`
        - $tokens = []
        - $collection = 'default'
