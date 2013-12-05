<?php
/*
Plugin Name: WooCommerce Skrill Gateway
Plugin URI: http://dev.pathtoenlightenment.net/shop
Description: A payment gateway for Skrill (https://www.skrill.com/). A Skrill merchant account is required for this gateway to work properly.
Version: 1.0.3.131201
Author: Diego Zanella
Author URI: http://dev.pathtoenlightenment.net
License: GPLv3
*/

/*  Copyright 2013 Diego Zanella (support@pathtoenlightenment.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    GPL3: http://www.gnu.org/licenses/gpl-3.0.txt
*/
if(preg_match('#' . basename(__FILE__ ) . '#', $_SERVER['PHP_SELF'])) {
  die('You are not allowed to call this page directly.');
}

require_once 'src/plugin-main.php';
