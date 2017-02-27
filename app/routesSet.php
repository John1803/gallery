<?php

use Core\Routing\RoutesSet;
use Core\Routing\Route;

$routesSet = new RoutesSet();

/**
 * Root albums on main page
 */

$routesSet->add("gallery_root_albums", new Route("/gallery",
    "/^\\/gallery$/",
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


