<?php

/**
 * Koncepcija:
 *   - klases faila nosaukuma extension - php
 * @param string $class_name
 * @return boolean
 */

kls_autoload::Register();

class kls_autoload {

    public static function Register() {
        return spl_autoload_register(array('kls_autoload', 'Load'));
    }

    public static function Load($class_name) {

        $sFile = ltrim($class_name, '\\');
        $sFile = CMS_EXTRAPATH.str_replace('\\', '/', $class_name).'.php';

        if (file_exists($sFile)) {
            require $sFile;
            return TRUE;
        }

        /**
         * direktory library
         */
        if (file_exists(CMS_LIBPATH . $class_name . '.php')) {
            include CMS_LIBPATH . $class_name . '.php';
            return TRUE;
        }

        /**
         * direktory library/sys
         */
//    if (file_exists(CMS_LIBPATH.CMS_SYSPATH.$class_name.'.php')){
//        include CMS_LIBPATH.CMS_SYSPATH.$class_name.'.php';
//        return TRUE;
//    }

        /**
         * EXTRA direktorijs
         */
        $sFilePath = CMS_EXTRAPATH . $class_name . '.php';
        //echo $sFilePath;
        if (file_exists($sFilePath)) {
            include $sFilePath;
            return TRUE;
        }

        $sFilePath = CMS_EXTRAPATH . str_replace('_', '/', $class_name) . '.php';
        if (file_exists($sFilePath)) {
            include $sFilePath;
            return TRUE;
        }
        
        $sFilePath = CMS_EXTRAPATH . $class_name . '/' . $class_name . '.php';
        //echo $sFilePath;
        if (file_exists($sFilePath)) {
            include $sFilePath;
            return TRUE;
        }

        $sFilePath = CMS_EXTRAPATH . $class_name . '/Classes/' . $class_name . '.php';
        //echo $sFilePath;
        if (file_exists($sFilePath)) {
            include $sFilePath;
            return TRUE;
        }

        return FALSE;
    }

}