<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Homepage - shows the search form
$routes->get('/', 'Weather::index');

// City search - processes the search and shows matching cities
$routes->get('weather/search', 'Weather::search');

// Weather forecast - shows weather for the selected city
$routes->get('weather/forecast', 'Weather::forecast');
