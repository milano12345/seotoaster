<?php

class Tools_System_SystemNotifications {

    const OPTIMIZED_EMAIL = 'optimizedemail';

    private static $_htmlRenderer = null;

    /**
     * @return Zend_View|null
     */
    private static function _getHtmlRenderer()
    {
        if(self::$_htmlRenderer === null) {
            $websiteHlpr = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
            self::$_htmlRenderer = new Zend_View(array(
                'scriptPath' => $websiteHlpr->getPath() . 'seotoaster_core/application/views/scripts/backend/systemnotifications/'
            ));
        }
        self::$_htmlRenderer->websiteUrl = $websiteHlpr->getUrl();
        return self::$_htmlRenderer;
    }

    public static function sendHtmlToEmails($page)
    {
        if($page instanceof Application_Model_Models_Page) {
            $renderer         = self::_getHtmlRenderer();

            $renderer->pageUrl = $page->getUrl();
            $renderer->pageName = $page->getNavName();

            $optimizedEmailBody = $renderer->render(self::OPTIMIZED_EMAIL . '.phtml');
        }

        if(!empty($optimizedEmailBody)) {
            self::sendEmails('optimizedNotifications', $optimizedEmailBody);
        }
    }

    public static function sendEmails($param, $notificationContent)
    {
        if(!empty($param) && !empty($notificationContent)) {
            $configHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
            $translator = Zend_Registry::get('Zend_Translate');

            $currentConfigParams = $configHelper->getConfig($param);

            if(!empty($currentConfigParams)) {
                $bccEmails = explode(',', $currentConfigParams);
                $validBssEmails = array();

                if(is_array($bccEmails) && !empty($bccEmails)) {
                    $emailValidation = new Tools_System_CustomEmailValidator();
                    foreach($bccEmails as $email){
                        if(!$emailValidation->isValid(trim($email))){
                            continue;
                        }
                        array_push($validBssEmails, $email);
                    }
                }

                if(!empty($validBssEmails)) {
                    $adminEmail = $configHelper->getConfig('adminEmail');

                    $adminEmail = !empty($adminEmail) ? $adminEmail : 'admin@localhost';

                    $mailer   = Tools_Mail_Tools::initMailer();
                    $subject = $translator->translate('System notifications');
                    $mailer->setMailFrom($adminEmail);
                    $mailer->setMailTo($validBssEmails);
                    $mailer->setBody($notificationContent);
                    $mailer->setSubject($subject);

                    try {
                        $mailer->send();
                        return true;
                    } catch (Exception $e) {
                        return false;
                    }
                }
            }
        }
        return false;
    }

}