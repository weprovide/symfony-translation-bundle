<?php

namespace WeProvide\TranslationBundle\Repository;

use JMS\TranslationBundle\Translation\Dumper\YamlDumper;
use JMS\TranslationBundle\Translation\FileWriter;
use JMS\TranslationBundle\Translation\LoaderManager;
use Symfony\Component\HttpKernel\Config\FileLocator;

class TranslationRepository
{
    /** @var FileLocator */
    protected $fileLocator;

    /** @var LoaderManager */
    protected $loaderManager;

    /** @var array */
    protected $config;


    /**
     * TranslationRepository constructor.
     * @param FileLocator   $fileLocator
     * @param LoaderManager $loaderManager
     * @param array         $config
     */
    public function __construct(FileLocator $fileLocator, LoaderManager $loaderManager, array $config)
    {
        $this->fileLocator   = $fileLocator;
        $this->loaderManager = $loaderManager;
        $this->config        = $config;
    }


    /**
     * @param null $locale
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    private function getCatalogue($locale = null)
    {
        $defaultLocale = $this->config['default_locale'];
        $resourcePath  = $this->config['resource'];
        $resourcePath  = $this->fileLocator->locate($resourcePath); // TODO: might throw error if path does not exist or is empty, maybe we should mkdir the path?
        $catalogue     = $this->loaderManager->loadFromDirectory($resourcePath, ($locale ? $locale : $defaultLocale));

        return $catalogue;
    }

    /**
     * Returns total number of translations.
     *
     * @return int
     */
    public function getCount()
    {
        $translations = $this->findAll();

        return count($translations);
    }

    /**
     * Retrieves all translations in a domain identfied by id.
     *
     * @param $domain
     * @param $id
     * @return array
     */
    public function getTranslations($domain, $id)
    {
        $translations     = array();
        $supportedLocales = $this->config['locales'];
        foreach ($supportedLocales as $supportedLocale) {
            $catalogue                      = $this->getCatalogue($supportedLocale);
            $collection                     = $catalogue->getDomain($domain);
            $translation                    = $collection->get($id);
            $translations[$supportedLocale] = $translation;
        }

        return $translations;
    }

    /**
     * Updates a translation.
     *
     * @param $domain
     * @param $id
     * @param $localeString
     */
    public function updateTranslation($domain, $id, $localeString)
    {
        $resourcePath = $this->config['resource'];
        $resourcePath = $this->fileLocator->locate($resourcePath);
        $yamlDumper   = new YamlDumper();
        $fileWriter   = new FileWriter(array("yml" => $yamlDumper));
        foreach ($localeString as $locale => $trans) {
            $file      = $resourcePath."/".$domain.".".$locale.".yml";
            $catalogue = $this->getCatalogue($locale);
            $message   = $catalogue->get($id, $domain);
            $message->setLocaleString($trans);
            $catalogue->set($message);
            $fileWriter->write($catalogue, $domain, $file, "yml");
        }
    }


    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier.
     * @param int|null $lockMode    One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        // TODO: not possible because $id is not unique.
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        $catalogue    = $this->getCatalogue();
        $translations = array();

        foreach ($catalogue->getDomains() as $domainKey => $domain) {
            if ($domainKey != "WeProvideTranslationBundle") {
                foreach ($domain->all() as $message) {
                    $translations[] = $message;
                }
            }
        }

        return $translations;
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $translations = $this->findAll();

        // Apply criteria (filter)   TODO: do more like sonata does, try out of the box
        foreach ($criteria as $key => $criterion) {
            if (($filterField = str_replace('_filter_', '', $key)) !== $key &&
                ($criterion = array_filter($criterion))) {
                $translations = array_filter($translations, function ($translation) use ($filterField, $criterion) {
                    $func  = 'get'.$filterField;
                    $value = $translation->$func();
                    if (isset($criterion['value'])) {
                        return (strpos(strtolower($value), strtolower($criterion['value'])) !== false);
                    }

                    return true;
                });
            }
        }


        // TODO: apply sorting

        // Apply pagination.
        if ($limit || $offset) {
            $translations = array_slice($translations, ($offset ? $offset : 0), ($limit ? $limit : null));
        }

        return $translations;
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @return array
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getTranslations($criteria['domain'], $criteria['id']);
    }
}