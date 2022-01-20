<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

require_once (_PS_MODULE_DIR_ . "nldemo/classes/nlDemoClass.php");

class Nldemo extends Module implements WidgetInterface
{
    protected $config_form = false;
	private $templateFile;

    public function __construct()
    {
        $this->name = 'nldemo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'NL ';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('NlDemo');
        $this->description = $this->l('Demo patekti pastabą pardavėjui');
        $this->confirmUninstall = $this->l('Ar tikrai norite išdiegti šį modulį?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
		$this->templateFile = 'module:nldemo/views/templates/hook/form.tpl';
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    public function getContent()
    {
        $html = "";

        if (Tools::getValue('id_nldemo')) {
            $resultAction = false;

            $id = Tools::getValue('id_nldemo');
            $note = new nlDemoClass($id);

            if (Tools::getValue('updatenldemoaloui')) {
                $note->active = true;
                if ($note->save())
                    $resultAction = true;

            }
            if (Tools::isSubmit('deletenldemoaloui')) {
                if ($note->delete())
                    $resultAction = true;
            }

            if ($resultAction)
                $html .= "<div class='alert alert-success' >Pavyko </div>";
            else
                $html .= "<div class='alert alert-error' >Įvyko klaida</div>";

        }
        $data = $this->getAllRecord();
        //print_r($data);
        // List of notes in module admin
        $helper = new HelperList();
        $helper->identifier = "Klientų pastabos";
        $helper->shopLinkType = null;
        $helper->actions = array('edit', 'delete');
        $helper->title = $this->displayName;
        $helper->table = $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $html .= $helper->generateList($data, array(
            'id_nldemo' => array(
                'title' => "ID",
                'width' => 80,
                'search' => true,
                'orderby' => true
            ),
            'id_product' => array(
                'title' => "Produktas"
            ),
            'note' => array(
                'title' => "Pastaba"
            ),
            'id_customer' => array(
                'title' => "Pirkėjo id"
            )
        ));
        return $html;
    }

    public function hookHeader()
    {
        //$this->context->controller->addJS($this->_path.'/views/js/front.js'); //no data for now
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
	
	public function renderWidget($hookName, array $configuration)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $message = '';

        if (Tools::isSubmit('note')) {
            $note = new nlDemoClass();
            $note->id_product = Tools::getValue('id_product');
            $note->note = Tools::getValue('note');
            $note->id_user = $this->context->customer->id;

            if ($note->save())
                $message = true;
            else {
                $message = false;
            }
        }

        return array(
            'message' => $message,
        );
    }

    protected function getAllRecord()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('nldemo', 'pn');
        $sql->innerJoin('customer', 'c', 'pn.id_user = c.id_customer');
        return DB::getInstance()->executeS($sql);
    }
}
