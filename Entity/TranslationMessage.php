<?php

namespace WeProvide\TranslationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationMessage
 *
 * @ORM\Table(name="translation_message")
 * @ORM\Entity(repositoryClass="WeProvide\TranslationBundle\Repository\TranslationMessageRepository")
 */
class TranslationMessage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;


    /**
     * @ORM\OneToMany(targetEntity="TranslationMessageLocale", mappedBy="message", cascade={"all"}, orphanRemoval=true)
     */
    private $locales;

    /**
     * TranslationMessage constructor.
     */
    public function __construct()
    {
        $this->locales = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return TranslationMessage
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return TranslationMessage
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * @param ArrayCollection $locales
     *
     * @return TranslationMessage
     */
    public function setLocales(ArrayCollection $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLocales()
    {
        return $this->locales;
    }

    public function addLocale(TranslationMessageLocale $locale)
    {
        $locale->setMessage($this);
        $this->locales[] = $locale;

        return $this;
    }

    public function removeLocale(TranslationMessageLocale $locale)
    {
        $this->locales->removeElement($locale);

        return $this;
    }
}

