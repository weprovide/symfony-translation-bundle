<?php

namespace WeProvide\TranslationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WeProvide\TranslationBundle\Entity\TranslationMessage;
use WeProvide\TranslationBundle\Entity\TranslationMessageLocale;
use WeProvide\TranslationBundle\Event\TranslationReextractEvent;
use WeProvide\TranslationBundle\Event\TranslationUpdateEvent;
use WeProvide\TranslationBundle\Repository\TranslationMessageLocaleRepository;
use WeProvide\TranslationBundle\Repository\TranslationMessageRepository;
use WeProvide\TranslationBundle\Repository\TranslationRepository;

class TranslationMessageAdminController extends CRUDController
{
    /**
     * @return RedirectResponse
     * @throws \Exception
     */
    public function extractAction()
    {
        $event = new TranslationReextractEvent();
        $this->get('event_dispatcher')->dispatch(
            'translation.reextract',
            $event
        );

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')->trans('Transalations extraction will start', array(), 'WeProvideTranslationBundle')
        );

        return new RedirectResponse($this->admin->generateUrl('list'));

    }
}
