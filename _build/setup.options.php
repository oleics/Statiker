<?php
$output = '';
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output = '<h2>Statiker Installer</h2><p>Thanks for installing Statiker! Please review the setup options below before proceeding.</p><br />';
        break;
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '<h2>Statiker UnInstaller</h2><p>Thanks for installing Statiker! Please review the setup options below before proceeding.</p><br />';
        break;
}
return $output;