<?php
$router = new \router\Router();
$router->route('/', 'VideoController@getAll');
$router->route('/video/upload/{id}', 'VideoController@getAll');
$router->route('/video/delete', 'VideoController@delete');
$router->route('/video/edit/{id}', "VideoController@loadEdit");
$router->route('/video/{id}', 'VideoController@getById');
$router->route('/trending', 'VideoController@getTrending');
$router->route('/history', 'VideoController@getHistory');
$router->route('/watchlater', 'VideoController@getWatchLater');
$router->route('/likedvideos', 'VideoController@getLikedVideos');
$router->route('/library/{id}', 'VideoController@getByOwnerId');
$router->route('/video/user/{id}', 'VideoController@getByOwnerId');
$router->route('/user/login', 'UserController@login');
$router->route('/user/register', 'UserController@register');
$router->route('/user/edit', 'UserController@edit');
$router->route('/user/logout', 'UserController@logout');
$router->route('/user/profile/{id}', 'UserController@getById');
$router->route('/subscriptions/{id}', 'UserController@subscriptions');
//TODO GET parameter followed_id
$router->route('/user/follow/{id}', 'UserController@follow');
$router->route('/video/{id}/status/{id}', 'UserController@reactVideo');
//TODO isfollowing method + all other user methods
$router->route('/user/unfollow/{id}', 'UserController@unfollow');
$router->route('/user/followed/{id}', 'UserController@clickedUser');
$router->route('/playlist/create', 'PlaylistController@create');
$router->route('/myplaylists/{id}', 'PlaylistController@getMyPlaylists');
$router->route('/playlist/{id}', 'PlaylistController@clickedPlaylist');
$router->route('/playlist/{id}/video/{id}', 'PlaylistController@addToPlaylist');
$router->route('/search', 'SearchController@search');
$router->route('/comment/add', 'CommentController@add');
$router->route('/comment/delete/{id}', 'CommentController@delete');
//TODO is reacting
$router->route('/comment/react/{id}/{id}', 'CommentController@react');
$router->route('/view/login', 'ViewController@viewRouter');
$router->route('/view/register', 'ViewController@viewRouter');
$router->route('/view/editProfile', 'ViewController@viewRouter');
$router->route('/view/upload', 'ViewController@viewRouter');
$router->route('/view/createplaylist', 'ViewController@viewRouter');
$router->route('/view/profile', 'ViewController@viewRouter');