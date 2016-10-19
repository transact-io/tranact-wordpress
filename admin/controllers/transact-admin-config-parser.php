<?php
namespace Transact\Admin\Config\Parser;

/**
 * Class AdminSettingsMenuExtension
 */
class AdminConfigParser
{
    /**
     * Holds Config Array configuration
     *
     * @var array
     */
    private $config;

    /**
     * Parses config.ini
     */
    public function __construct()
    {
        $this->config = parse_ini_file(CONFIG_PATH);
    }

    /**
     * Retrieves Api authentication url
     *
     * @return string apu authetication url
     */
    public function getValidationUrl()
    {
        return $this->config['api_host'] . $this->config['api_authentication'];
    }

}
