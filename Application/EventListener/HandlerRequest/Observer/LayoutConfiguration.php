<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerRequest\Observer;

use SplSubject;
use Sfynx\AuthBundle\Application\EventListener\HandlerRequest\Generalisation\HandlerRequestInterface;

/**
 * Class LayoutConfiguration
 *
 * Sets the good layout configuration
 *
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class LayoutConfiguration implements HandlerRequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function update(SplSubject $subject)
    {
        $this->layout = $subject->container->getParameter('sfynx.template.theme.layout.front.pc') . $subject->param->init_pc_layout;
        if ($subject->request->cookies->has('sfynx-layout')) {
            $this->layout = $subject->request->cookies->get('sfynx-layout');
        }
        $this->screen = "layout";
        if ($subject->request->cookies->has('sfynx-screen')) {
            $this->screen = $subject->request->cookies->get('sfynx-screen');
        }
        if ($subject->param->is_browser_authorized
            && !$subject->request->cookies->has('sfynx-layout')
            && $subject->container->has("sfynx.browser.lib.mobiledetect")
            && $subject->container->has("sfynx.browser.lib.browscap")
        ) {
            $this->browser      = $subject->container->get("sfynx.browser.browscap")->getClient();
            $this->mobiledetect = $subject->container->get("sfynx.browser.mobiledetect")->getClient();
            if ($this->browser->isMobileDevice) {
                $this->screen = "layout-medium";
                if (!$this->mobiledetect->isTablet()) {
                    $this->screen = "layout-poor";
                }
                $this->layout = $subject->container->getParameter('sfynx.template.theme.layout.front.mobile')
                    . $subject->param->init_mobile_layout.'\\'
                    . $this->screen
                    . '.html.twig';
                $subject->request->setRequestFormat('mobile');
            }
        }
        $subject->request->attributes->set('sfynx-layout', $this->layout);
        $subject->request->attributes->set('sfynx-screen', $this->screen);
    }
}
