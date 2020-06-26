<?php

Route::get('media', 'MediaController@index')->name('media.index');
Route::post('media/upload', 'MediaController@store')->name('media.store');
Route::delete('media/{media}', 'MediaController@destroy')->name('media.destroy');

