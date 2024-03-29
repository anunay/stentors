<?php

/**
 * Module Users
 * Data management for the registered users.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormMembersProfile.php 826 2012-02-01 04:15:13Z ssoares $id
 */

/**
 * Form to manage specific data.
 * Fields will change for each project.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormMembersProfile.php 826 2012-02-01 04:15:13Z ssoares $id
 */
class FormMemberProfile extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = false;
        $this->_disabledLangSwitcher = true;
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }
        parent::__construct($options);
        $this->setAttrib('id', 'memberInfo');

        $this->addDisplayGroup(
            array(
                'MP_BirthDateDt',
                'MP_BirthDate',
                'MP_Age',
                'MP_School',
                'MP_SchoolYear',
                'MP_AssuSocNum',
                'MP_Phone',
                'MP_Section'
                ),
            'identity');
        $this->getDisplayGroup('identity')
            ->setLegend('Identification')
            ->setAttrib('class','infosFieldset')
            ->removeDecorator('DtDdWrapper');

        $this->addDisplayGroup(
            array('MP_CountryOrig',
                'MP_PassportNum',
                'MP_PassportExpiracyDateDt',
                'MP_PassportExpiracyDate',
                'MP_PassportBirthDate',
                'MP_PassportFirstName',
                'MP_PassportLastName'),
            'passport');
        $this->getDisplayGroup('passport')
            ->setAttrib('class','infosFieldset')
            ->setLegend('Passeport')
            ->removeDecorator('DtDdWrapper');
        $this->addDisplayGroup(
            array('MP_LiveWith',
                'MP_AgreePhotos',
                'MP_Notes'),
            'other');

        $this->getDisplayGroup('other')
            ->setAttrib('class','infosFieldset')
            ->removeDecorator('DtDdWrapper');

        $this->addDisplayGroup(
            array('MP_Category',
                'MP_YearsParticipate',
                'MP_IsStaff'),
            'otherRight');

        $this->getDisplayGroup('otherRight')
            ->setAttrib('class','infosFieldset')
            ->removeDecorator('DtDdWrapper');
        if (isset($options['baseDir']))
        {
            $url = $options['baseDir']
                . 'member/index/print-registration/id/'
                . $options['dataId'] . '/';
            $this->getView()->assign('printPage', $url);
        }
    }

    public function populate(array $values)
    {
        $baseDir = $this->getView()->BaseUrl();
        $content = '';
        $isSaved = false;

        $currentYear = date('Y', time());
        if (empty($values['MP_YearsParticipate']))
            $values['MP_YearsParticipate'] = $currentYear . ',';

        $isSaved = preg_match('/'.$currentYear.'/', $values['MP_YearsParticipate']);

        if (!$isSaved && $values['MP_Category'] == 46)
        {
            $lastChar = strrpos($values['MP_YearsParticipate'], ',', -1);
            if (!$lastChar)
                $values['MP_YearsParticipate'] .= ',';

            $values['MP_YearsParticipate'] .= $currentYear . ',';
        }
        parent::populate($values);

        if (!empty($values['firstP']))
        {
            $subject = '##ROLE## : ' . $this->getView()->link('##HREF##', '##FNAME## ##LNAME##');
            if ($values['firstP'][4])
                $subject .= " (reçu pour impôts)";

            $href = $baseDir . '/users/index/general/actionKey/edit/id/' . $values['firstP']['1'];
            $values['firstP']['1'] = $href;
            $content = str_replace(array('##ROLE##', '##HREF##', '##FNAME##', '##LNAME##'), $values['firstP'], $subject);

        }
        if (!empty($values['secP']))
        {
            $subject = '##ROLE## : ' . $this->getView()->link('##HREF##', '##FNAME## ##LNAME##');
            if ($values['secP'][4])
                $subject .= " (reçu pour impôts)";
            $href = $baseDir . '/users/index/general/actionKey/edit/id/' . $values['secP']['1'];
            $values['secP']['1'] = $href;
            $content .= '<br />';
            $content .= str_replace(array('##ROLE##', '##HREF##', '##FNAME##', '##LNAME##'), $values['secP'], $subject);

        }
        if (empty($values['firstP']) || empty($values['secP']))
        {
//            $img = <img title="Supprimer" alt="" src="">
            $img = $this->getView()->image('/extranet/icons/button_add.png', array('title' => 'Ajouter'));
            $href = $this->getView()->BaseUrl() . '/parent/index/list/actionKey/add/child/' . $values['MP_GenericProfileId'];
            $link = $this->getView()->link($href, $img . 'Ajouter les parents / représentants légaux', array('class' => 'addParents'));
            if (!empty($content)) $content .= '<br />';
            $content .= $link;
        }
        $firstP = new Cible_Form_Element_Html(
            'parents',
            array(
                'value' => $content
            )
        );
        $firstP->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'left clearLeft')
                    ),
                )
        );
        $this->getDisplayGroup('other')->addElement($firstP);
        $firstP->setOrder(1);
    }
}