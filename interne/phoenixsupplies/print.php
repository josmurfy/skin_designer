<?php

    /**
     *  Script that will generate printer friendly versions of referer pages
     *
     *  Don't call the class file directly! Call this file instead!
     *
     *  This work is licensed under the Creative Commons Attribution-NonCommercial-NoDerivs 2.5 License.
     *  To view a copy of this license, visit {@link http://creativecommons.org/licenses/by-nc-nd/2.5/} or send a letter to
     *  Creative Commons, 543 Howard Street, 5th Floor, San Francisco, California, 94105, USA.
     *
     *  For more resources visit {@link http://stefangabos.blogspot.com}
     *
     *  @name       print
     *  @package    print
     *  @version    1.0 (last revision: October 03, 2006)
     *  @author     Stefan Gabos <ix@nivelzero.ro>
     *  @copyright  (c) 2006 Stefan Gabos
     */

    // get the absolute path of the class. any further includes rely on this
    // and (on a windows machine) replace \ with /
    $absolutePath = preg_replace("/\\\/", "/", dirname(__FILE__));

    require $absolutePath."/class.printer.php";

    $printer = new printer();
    
    // DON'T FORGET TO SET THIS PROPERTY IN ORDER FOR YOUR IMAGES AND
    // ANY OTHER RELATIVE LINKS TO WORK
    // for the example script you can leave it like it is
    $printer->baseHREF = "";

    // change the default properties
    $printer->dropImages = true;
    
    // render page
    $printer->render();


?>
