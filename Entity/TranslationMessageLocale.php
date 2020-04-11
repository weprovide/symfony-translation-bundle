<?php

namespace WeProvide\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationMessageLocale
 *
 * @ORM\Table(name="translation_message_locale")
 * @ORM\Entity(repositoryClass="WeProvide\TranslationBundle\Repository\TranslationMessageLocaleRepository")
 */
class TranslationMessageLocale
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
     * @ORM\Column(name="locale", type="string", length=15)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;


    /**
     * @ORM\ManyToOne(targetEntity="TranslationMessage", inversedBy="locales")
     * @ORM\JoinColumn(name="translation_message_id", referencedColumnName="id", nullable=false)
     */
    private $message;


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
     * Set locale
     *
     * @param string $locale
     *
     * @return TranslationMessageLocale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return TranslationMessageLocale
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @param TranslationMessage $message
     *
     * @return TranslationMessageLocale
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return TranslationMessage
     */
    public function getMessage()
    {
        return $this->message;
    }
}

