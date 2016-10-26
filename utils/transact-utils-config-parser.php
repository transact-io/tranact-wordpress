<?php
namespace Transact\Utils\Config\Parser;

/**
 * Class ConfigParser
 */
class ConfigParser
{
    /**
     * Holds Config Array configuration
     *
     * @var array
     */
    private $config;

    /**
     * Key for API_HOST
     */
    const API_HOST = 'api_host';

    /**
     * key for API_AUTHENTICATION
     */
    const API_AUTHENTICATION = 'api_authentication';

    /**
     * Key for JS_LIBRARY
     */
    const JS_LIBRARY = 'js_xsact_library';

    /**
     * Parses config.ini
     */
    public function __construct()
    {
        $this->config = parse_ini_file(CONFIG_PATH);
    }

    /**
     * Returning configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returning JS api library url
     *
     * @return string js api library url
     */
    public function getJSLibrary()
    {
        return $this->config[self::JS_LIBRARY];
    }

    /**
     * Retrieves Api authentication url
     *
     * @return string apu authetication url
     */
    public function getValidationUrl()
    {
        return $this->config[self::API_HOST] . $this->config[self::API_AUTHENTICATION];
    }

}

