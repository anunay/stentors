<?php

/**
 * Cible
 *
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Save the last path visited
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_LastVisited extends Zend_View_Helper_Abstract
{


    /**
     * Save the url page visited
     * Example use:
     * Cible_View_Helper_LastVisited::saveThis($this->_request->getRequestUri());
     * @param string $url The page url.
     * @return void
     */
    public static function saveThis($url)
    {
        $_nbHist = 10;
        $urlArray = self::getLastVisited();
        $lastPg = new Zend_Session_Namespace('history');

        if (current($urlArray) != $url)
            array_unshift ($urlArray, $url);
        if (count($urlArray) > $_nbHist)
            array_pop ($urlArray);

        $lastPg->last = $urlArray;
    }
    /**
     * Remove the urls
     * 
     * @param string $url The page url.
     * @return void
     */
    public static function emptyUrls()
    {
        $lastPg = new Zend_Session_Namespace('history');
        $lastPg->unsetAll();
    }

    /**
     * Get the last visited page.
     *
     * @return string $path saved url of the last stored page.
     */
    public static function getLastVisited()
    {
        $path = array();

        $lastPg = new Zend_Session_Namespace('history');

        if (!empty($lastPg->last))
        {
            $path = $lastPg->last;

        }

        return $path;
    }

}