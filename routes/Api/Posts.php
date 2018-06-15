<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('posts', 'APIPostsController@index')->name('posts.index');
    Route::get('my-posts', 'APIPostsController@my')->name('posts.index');
    Route::post('posts/create', 'APIPostsController@create')->name('posts.create');
    Route::post('posts/edit', 'APIPostsController@edit')->name('posts.edit');
    Route::post('posts/show', 'APIPostsController@show')->name('posts.show');
    Route::post('posts/delete', 'APIPostsController@delete')->name('posts.delete');
});
?>