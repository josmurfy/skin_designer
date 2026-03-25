<?php

/**
 *  Class that creates printer friendly version of any referer page
 *
 *  This script helps you create printer friendly versions of your pages.
 *  All you need to do is to insert some tags in your pages, tags that will tell the script what needs to be printed from
 *  that specific page. An unlimited number of areas can be set for printing allowing you a flexible way of setting up
 *  the content to be printed
 *
 *  The output is template driven, meaning that you can customize the printer friendly versions of your pages by adding
 *  custom headers, footers, copyright information or whatever extra info that you find appropriate
 *
 *  The script can be instructed to transform links to a readable format (<a href="www.somesite.com">click here</a> will
 *  become click here [www.somesite.com]) or to remove img tags (<img src="pic.jpg" alt="picture"> will become
 *  [image: picture] or just [image] if no alt attribute is specified)
 *
 *  This script was inspired by PHPrint {@link http://www.tufts.edu/webcentral/phprint}
 *
 *  This work is licensed under the Creative Commons Attribution-NonCommercial-NoDerivs 2.5 License.
 *  To view a copy of this license, visit {@link http://creativecommons.org/licenses/by-nc-nd/2.5/} or send a letter to
 *  Creative Commons, 543 Howard Street, 5th Floor, San Francisco, California, 94105, USA.
 *
 *  For more resources visit {@link http://stefangabos.blogspot.com}
 *
 *  @name       printer
 *  @package    print
 *  @version    1.0 (last revision: October 03, 2006)
 *  @author     Stefan Gabos <ix@nivelzero.ro>
 *  @copyright  (c) 2006 Stefan Gabos
 *  @example    example.php
 *
 */

error_reporting(E_ALL);

class printer
{

    /**
     *  The web root of the site (without trailing slashes)
     *
     *  This is needed in order for relative links to work correctly
     *
     *  @var    string
     */
    var $baseHREF;

    /**
     *  Tag used to delimit start of the area to print
     *
     *  An unlimited number of areas to be printed can be delimited as long as
     *  they are not contained inside another defined area!
     *
     *  default is "<!-- PRINT: start -->" (without the quotes)
     *
     *  @var    string
     */
    var $startPrintTag;

    /**
     *  Tag used to delimit end of the area to print
     *
     *  An unlimited number of areas to be printed can be delimited as long as
     *  they are not contained inside another defined area!
     *
     *  default is "<!-- PRINT: stop -->" (without the quotes)
     *
     *  @var    string
     */
    var $stopPrintTag;

    /**
     *  Weather or not to convert links (anchors) to a readable format.
     *
     *  By default, when printing something like <a href="http://www.somesite.com">click</a>,
     *  the url will not be visible on the paper - just "click" will be shown.
     *
     *  When you set this property to TRUE the anchor from above will produce
     *  "click [http://www.somesite.com]" (without the quotes)
     *
     *  The script will convert all of these cases (single, double and no quotes):
     *
     *  <a href="http://www.somesite.com">click</a>
     *
     *  <a href='http://www.somesite.com'>click</a>
     *
     *  <a href=http://www.somesite.com>click</a>
     *
     *  default is TRUE
     *
     *  @var    boolean
     */
    var $convertLinks;

    /**
     *  Weather or not to cut out images from the print
     *
     *  When set to TRUE, all <img> tags will be replaced with the "[image:]" word
     *  (without the quotes) or, if image has the "alt" attribute set, with
     *  "[image:alt description]" (without the quotes)
     *
     *  Note that if you choose to drop the images you page layout may suffer modifications!
     *
     *  default is FALSE
     *
     *  @var    boolean
     */
    var $dropImages;
    
    /**
     *  Template folder to use
     *  Note that only the folder of the template you wish to use needs to be specified. Inside the folder
     *  you <b>must</b> have the <b>template.xtpl</b> file which will be automatically used
     *
     *  default is "default"
     *
     *  @var   string
     */
    var $template;

    /**
     *  In case of an error read this property's value to find out what went wrong
     *
     *  possible error values are:
     *
     *      - 0:  file could not be opened
     *      - 1:  the number of starting tags don't match the number of ending tags
     *      - 2:  areas overlap eachother
     *      - 3:  could not instantiate the templating engine
     *
     *  default is 0
     *
     *  @var integer
     */
    var $error;
    
    /**
     *  Constructor of the class
     *
     *  @access private
     */
    function printer()
    {
        // default values for the graph's properties
        // we do this so that the script will also work in PHP 4
        $this->error = 0;
        $this->template = "default";
        $this->dropImages = false;
        $this->convertLinks = true;
        $this->startPrintTag = "<!-- PRINT: start -->";
        $this->stopPrintTag = "<!-- PRINT: stop -->";
        $this->baseHREF = "";
    }

    /**
     *  This method will render the printer friendly version of the referer page
     */
    function render()
    {
        // print the page who called this page
        $page = $_SERVER["HTTP_REFERER"];

        // tries to open the page
        // note that the page is opened exactly the same way as any browser would open it!
        if ($handle = fopen($page, "rb")) {

            // if file opened successfully
            $pageContent = '';

            // read all its content in a variable
            while (!feof($handle)) {

                $pageContent .= fread($handle, 8192);

            }

            // close file
            fclose($handle);

            // read all starting tags positions into an array
            preg_match_all("/".quotemeta($this->startPrintTag)."/", $pageContent, $startTags, PREG_OFFSET_CAPTURE);

            // read all ending tags positions into an array
            preg_match_all("/".quotemeta($this->stopPrintTag)."/", $pageContent, $stopTags, PREG_OFFSET_CAPTURE);

            // if there are as many starting tags as ending tags
            if (count($startTags) == count($stopTags)) {

                // this is an array that groups start-end pairs
                $tagsArray = array();

                // populate the array
                for ($i = 0; $i < count($startTags[0]); $i++) {

                    $tagsArray[] = array($startTags[0][$i][1], $stopTags[0][$i][1]);

                }

                // at this stage the $tagsArray[] array holds all the pairs of
                // starting-ending positions of printable areas

                // checks if there are areas that are crossing each other
                // by comparing the values of the array
                foreach ($tagsArray as $subjectKey=>$subjectValues) {

                    // with all the values of the array
                    foreach ($tagsArray as $searchKey=>$searchValues) {

                        // except the one that is checked
                        if ($subjectKey != $searchKey) {

                            // checks if the area crosses other areas
                            if (
                                ($subjectValues[0] >= $searchValues[0] && $subjectValues[0] <= $searchValues[1]) ||
                                ($subjectValues[1] >= $searchValues[0] && $subjectValues[1] <= $searchValues[1])
                            ) {

                                // save the error level and stop the execution of the script
                                $this->error = 2;
                                return false;

                            }

                        }

                    }

                }

                // if everything is ok
                // retrieve from the page only the content that needs to be printed
                $printContent = '';
                foreach ($tagsArray as $offset) {

                    $printContent .= substr($pageContent, $offset[0] + strlen($this->startPrintTag), $offset[1] - $offset[0] - strlen($this->startPrintTag));

                }

                // if links are to be converted to a readable format
                if ($this->convertLinks) {

                    // until there are links left to convert
                    while (preg_match("/\<a.*href\s*=\s*\'([^\']*)\'[^\>]*\>(.*)\<\/a\>|\<a.*href\s*=\s*\"([^\"]*)\"[^\>]*\>(.*)\<\/a\>|\<a.*href\s*=\s*([^\s]*)\s*[^\>]*\>(.*)\<\/a\>/i", $printContent, $matches) > 0) {

                        // convert links
                        $printContent = preg_replace("/\<a.*href\s*=\s*\'([^\']*)\'[^\>]*\>(.*)\<\/a\>|\<a.*href\s*=\s*\"([^\"]*)\"[^\>]*\>(.*)\<\/a\>|\<a.*href\s*=\s*([^\s]*)\s*[^\>]*\>(.*)\<\/a\>/i", "\$2\$4\$6 [\$1\$3\$5]", $printContent, 1);

                    }

                }

                // if links are to be converted to a readable format
                if ($this->dropImages) {

                    // until there are links left to convert
                    while (preg_match("/\<img[^\>]*\>/", $printContent, $matches) > 0) {

                        // if image has the alt attribute set
                        if (preg_match("/alt\s*?=\s*?\"([^\"]*)\"|alt\s*?=\s*?\'([^\']*)\'|alt\s*?=\s*?([^\s]*)\s/i", $matches[0], $altText)) {
                        
                            // replace the img tag with [image: alt content]
                            $printContent = preg_replace("/\<img[^\>]*\>/", "[image:".$altText[1]."]", $printContent, 1);

                        // if no alt attribute is set for the image
                        } else {

                            // replace the img rag with [image]
                            $printContent = preg_replace("/\<img[^\>]*\>/", "[image]", $printContent, 1);
                        }

                    }

                }

                // get the absolute path of the class. any further includes rely on this
                // and (on a windows machine) replace \ with /
                $this->absolutePath = preg_replace("/\\\/", "/", dirname(__FILE__));

                // get the relative path of the class. ( by removing $_SERVER["DOCUMENT_ROOT"] from the it)
                // any HTML reference (to scripts, to stylesheets) in the template file should rely on this
                $this->relativePath = preg_replace("/".preg_replace("/\//", "\/", $_SERVER["DOCUMENT_ROOT"])."/i", "", $this->absolutePath);

                // if the xtemplate class is not already included
                if (!class_exists("XTemplate")) {

                    // if the file exists
                    if (file_exists($this->absolutePath."/includes/class.xtemplate.php")) {

                        // include the xtemplate class
                        require_once $this->absolutePath."/includes/class.xtemplate.php";

                    // if the file does not exists
                    } else {

                        // save the error level and stop the execution of the script
                        $this->error = 3;
                        return false;

                    }

                }

                // create a new XTemplate object using the specified template
                $xtpl = new XTemplate($this->absolutePath."/templates/".$this->template."/template.xtpl");
                
                // assign relative path to the template folder
                // any HTML reference (to scripts, stylesheets) in the template file should rely on this
                $xtpl->assign("templatePath", $this->relativePath."/templates/".$this->template."/");
                
                $xtpl->assign("BASE_HREF", urlencode($this->baseHREF));
                
                // content to be printed
                $xtpl->assign("printContent", $printContent);

                // wrap up the generation of the printer friendly version of the page
                $xtpl->parse("main");
                $xtpl->out("main");

            // if different number of starting and ending tags
            } else {
            
                // save the error level and stop the execution of the script
                $this->error = 1;
                return false;

            }

        // if page could not be opened (i.e. no access rights)
        } else {
        
            // save the error level and stop the execution of the script
            $this->error = 0;
            return false;

        }

        // returns true if everything went ok
        return true;
        
    }
    
}
?>
