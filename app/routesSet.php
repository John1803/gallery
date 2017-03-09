<?php

use Core\Routing\RoutesSet;
use Core\Routing\Route;

$routesSet = new RoutesSet();

/**
 * Root albums on main page
 */

$routesSet->add("gallery_root_albums", new Route("/",
    "/^\\/$/",
    ["controllerAction" => "\Gallery\Controllers\GalleryController:indexAction"]));

/**
 * Images and Subalbums of certain album
 */

$routesSet->add("gallery_subalbums_and_images", new Route("/gallery/albums/images/album/{id}",
    "/^\\/gallery\\/albums\\/images\\/album\\/(?<id>[^\\/]+)$/",
    ["controllerAction" => "\Gallery\Controllers\GalleryController:showImagesAlbumsAction"]
    ));

$routesSet->add("new_album", new Route("/album/new",
    "/^\\/album\\/new$/",
    ["controllerAction" => "\Gallery\Controllers\AlbumController:createAction"]
    ));


$routesSet->add("album_form", new Route("/album/form",
    "/^\\/album\\/form$/",
    ["controllerAction" => "\Gallery\Controllers\AlbumController:albumFormCreationAction"]
    ));

$routesSet->add("album_edit", new Route("/album/edit",
    "/^\\/album\\/edit$/",
    ["controllerAction" => "\Gallery\Controllers\AlbumController:editAction"]
    ));

$routesSet->add("edit_album_id", new Route("/edit/album/{id}",
    "/^\\/edit\\/album\\/(?<id>[\d^\\/]+)$/",
    ["controllerAction" => "\Gallery\Controllers\AlbumController:editAlbumAction"]
    ));
$routesSet->add("image_form_upload", new Route("/image/form-upload",
    "/^\\/image\\/form-upload$/",
    ["controllerAction" => "\Gallery\Controllers\ImageController:imageFormAction"]
    ));
$routesSet->add("image_upload", new Route("/upload/image",
    "/^\\/upload\\/image$/",
    ["controllerAction" => "\Gallery\Controllers\ImageController:uploadAction"]
    ));




