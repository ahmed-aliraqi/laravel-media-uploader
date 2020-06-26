<?php

Route::get('media', 'MediaController@index')->name('uploader.media.index');
Route::post('media/upload', 'MediaController@store')->name('uploader.media.store');
Route::delete('media/{media}', 'MediaController@destroy')->name('uploader.media.destroy');

