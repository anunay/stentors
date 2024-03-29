<?php

abstract class Cible_FunctionsModules
{
    public static function getModulesList()
    {
        $modules = Zend_Registry::get("db");
        $select = $modules->select()
            ->from('Modules_ControllersActionsPermissions', array(
                'MCAP_ControllerTitle',
                'MCAP_ControllerActionTitle',
                'MCAP_PermissionTitle',
            ))->joinInner('Modules', 'Modules_ControllersActionsPermissions.MCAP_ModuleID = Modules.M_ID', array('M_MVCModuleTitle'))
            ->where('MCAP_Position > 0')
            ->order('Modules.M_ID ASC')
            ->order('Modules_ControllersActionsPermissions.MCAP_Position ASC');

        $module_list = $modules->fetchAll($select);

        $tmp = array();
        foreach ($module_list as $mod)
        {
            if (!isset($tmp[$mod['M_MVCModuleTitle']]))
            {

                $tmp[$mod['M_MVCModuleTitle']] = array(
                    'M_MVCModuleTitle' => $mod['M_MVCModuleTitle'],
                    'actions' => array()
                );
            }

            array_push($tmp[$mod['M_MVCModuleTitle']]['actions'], array(
                'controller' => $mod['MCAP_ControllerTitle'],
                'action' => $mod['MCAP_ControllerActionTitle'],
                'permission' => $mod['MCAP_PermissionTitle'],
                )
            );
        }

        return $tmp;
    }

    public static function getModuleIDByName($module_name)
    {
        $db = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Modules', array('M_ID'))
            ->where('Modules.M_MVCModuleTitle = ?', $module_name);

        $col = $db->fetchOne($select);

        if (!$col)
            Throw new Exception('Module not found');

        return $col;
    }

    public static function getModules()
    {
        $db = Zend_Registry::get("db");
        $select = $db->select()
            ->distinct()
            ->from('Modules', array('M_MVCModuleTitle', 'M_ID'));

        $data = $db->fetchAll($select);

        return $data;
    }
    public static function getModuleNameByID($id = null)
    {
        $db = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Modules', array('M_MVCModuleTitle'))
            ->where('Modules.M_ID = ?', $id);

        $col = $db->fetchOne($select);

        if (!$col)
            Throw new Exception('Module not found');

        return $col;
    }

    public static function getLocalizedModuleTitle($module)
    {
        return ' > ' . Cible_Translation::getCibleText("Module_$module");
    }

    public static function getAvailableViews($module_name)
    {
        $db = Zend_Registry::get("db");
        $select = $db->select()
            ->from('ModuleViews', array('MV_Name'))
            ->join('Modules', 'ModuleViews.MV_ModuleID = Modules.M_ID')
            ->where('Modules.M_MVCModuleTitle = ?', $module_name);

        return $db->fetchAll($select);
    }

    public static function getPagePerModuleView($module_id, $view_name)
    {
        $db = Zend_Registry::get("db");

        $select = $db->select()
            ->from('ModuleCategoryViewPage', array())
            ->join('ModuleViews', 'ModuleViews.MV_ID = ModuleCategoryViewPage.MCVP_ViewID', array())
            ->join('ModuleViewsIndex', 'ModuleViewsIndex.MVI_ModuleViewsID = ModuleCategoryViewPage.MCVP_ViewID', array('MVI_ActionName'))
            ->join('PagesIndex', 'PagesIndex.PI_PageID = ModuleCategoryViewPage.MCVP_PageID', array('PI_PageIndex'))
            ->where('ModuleCategoryViewPage.MCVP_ModuleID = ?', $module_id)
            ->where('ModuleViews.MV_Name = ?', $view_name)
            ->where('ModuleViewsIndex.MVI_LanguageID = ?', Zend_Registry::get('languageID'));

        $row = $db->fetchRow($select);

        if (!$row)
            return '';

        return "{$row['PI_PageIndex']}/{$row['MVI_ActionName']}";
    }

    /**
     * Fetch modules using profiles.
     *
     * @return array
     */
    public static function modulesProfile()
    {
        Zend_Registry::set('pwdOn', false);
        $modules = array();
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('Modules', array('M_ID', 'M_Title', 'M_MVCModuleTitle', 'M_NeedAuth'))
            ->where('M_UseProfile = ?', 1);

        $data = $db->fetchAll($select);

        foreach ($data as $module)
        {
            $modules[$module['M_ID']] = array(
                'M_Title' => $module['M_Title'],
                'M_ID' => $module['M_ID'],
                'M_MVCModuleTitle' => $module['M_MVCModuleTitle'],
                'M_NeedAuth' => $module['M_NeedAuth']);

            if ($module['M_NeedAuth'])
                Zend_Registry::set('pwdOn', true);
        }

        return $modules;
    }
    /**
     * Fetch modules to build filters.
     *
     * @return array
     */
    public static function modulesFilters()
    {
        $modules = array();
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('Modules', array('M_ID', 'M_Title', 'M_MVCModuleTitle'))
            ->where('M_AllowFilters = ?', 1);

        $data = $db->fetchAll($select);

        foreach ($data as $module)
        {
            $modules[$module['M_ID']] = array(
                'M_Title' => $module['M_Title'],
                'M_ID' => $module['M_ID'],
                'M_MVCModuleTitle' => $module['M_MVCModuleTitle']);
        }

        return $modules;
    }
}
