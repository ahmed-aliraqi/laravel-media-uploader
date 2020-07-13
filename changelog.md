# Release Notes for Laravel Media Uploader
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
