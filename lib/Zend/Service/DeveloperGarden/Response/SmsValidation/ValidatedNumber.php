<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ValidatedNumber.php 586 2011-08-30 21:35:23Z ssoares $
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_Response_SmsValidation_ValidatedNumber
{
    /**
     * the number
     * @var string
     */
    public $number = null;

    /**
     * is valid until this date
     * @var string
     */
    public $validUntil = null;

    /**
     * returns the number
     *
     * @return number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * returns the valid until date
     *
     * @return string
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }
}