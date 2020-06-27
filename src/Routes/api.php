<?php

Route::get('uploader/media', 'MediaController@index')->name('uploader.media.index');
Route::post('uploader/media/upload', 'MediaController@store')->name('uploader.media.store');
Route::delete('uploader/media/{media}', 'MediaController@destroy')->name('uploader.media.destroy');
