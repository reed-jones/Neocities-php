<?php

namespace ReedJones\Neocities;

/**
 *  Neocities API Wrapper.
 *
 * The Neocities class is a wrapper around the Neocities Api.
 * Allowing you to
 *   - get information about your website
 *   - get your api key
 *   - upload files to your website
 *   - delete files from your website
 *
 *  @author Reed Jones
 */
class Neocities
{
    /**
     * Your username for the Neocities website
     * Not needed if providing an API key instead.
     *
     * @var string
     */
    public $username;

    /**
     * Your password for the Neocities website.
     * Not needed if providing an API key instead.
     *
     * @var string
     */
    private $password;

    /**
     * Your api key for the Neocities website.
     * Not needed if providing a username/password instead.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Set options on how to interact with the api.
     * currently URL is supported which defaults to https://neocities.org
     * if not provided.
     *
     * @var array
     */
    public $options;

    /**
     * Class Constructor.
     *
     * Initializes internal variables (credentials, options)
     * for user later
     */
    public function __construct($credentials, $options = [])
    {
        if (isset($credentials['username'])) {
            $this->username = $credentials['username'];
        }

        if (isset($credentials['password'])) {
            $this->password = $credentials['password'];
        }

        if (isset($credentials['apiKey'])) {
            $this->apiKey = $credentials['apiKey'];
        }

        $validateAuth = $this->ApiAuth();

        if (isset($options['url'])) {
            $this->options = $this->parseUrl($options['url']);
        } else {
            $this->options = $this->parseUrl('https://neocities.org');
        }
    }

    /**
     * Lists all files on your site.
     *
     * @return mixed
     */
    public function list()
    {
        return $this->Get('list');
    }

    /**
     * Gets info about your site.
     *
     * @return mixed
     */
    public function info()
    {
        return $this->Get('info');
    }

    /**
     * Retrieves api key.
     *
     * @return mixed
     */
    public function key()
    {
        return $this->Get('key');
    }

    /**
     * Upload files to the server.
     *
     * @param array $files
     *
     * @return mixed
     */
    public function upload($files)
    {
        foreach ($files as $name => &$place) {
            $place = curl_file_create($place);
        }

        return $this->Post('upload', $files);
    }

    /**
     * Delete files from the server.
     *
     * @param array $files
     *
     * @return mixed
     */
    public function delete($files)
    {
        foreach ($files as &$file) {
            $file = "filenames[]={$file}";
        }

        return $this->Post('delete', implode('&', $files));
    }

    /**
     * Retrieves data from api endpoint via GET method.
     *
     * @param string $method
     *
     * @return mixed $result
     */
    private function Get($method)
    {
        $endpoint = "api/{$method}";
        $options = $this->options;
        $url = $options['subdomain'] ? $options['subdomain'].'.' : '';
        $url .= $options['domain'].'.'.$options['tld'];

        $ch = curl_init();

        if (isset($this->apiKey)) {
            $neo_api = "{$options['protocol']}://{$url}/{$endpoint}";
            $authorization = "Authorization: Bearer {$this->apiKey}";
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        } else {
            $neo_api = "{$options['protocol']}://{$this->ApiAuth()}@{$url}/{$endpoint}";
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $neo_api);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Sends data to the api endpoint via POST method.
     *
     * @param string $method
     * @param mixed $data
     *
     * @return mixed $result
     * @throws \Exception
     */
    private function Post($method, $data)
    {
        $endpoint = "api/{$method}";
        $options = $this->options;
        $url = $options['subdomain'] ? $options['subdomain'].'.' : '';
        $url .= $options['domain'].'.'.$options['tld'];

        $ch = curl_init();
        if (isset($this->apiKey)) {
            // echo 'API'; die;
            $neo_api = "{$options['protocol']}://{$url}/{$endpoint}";
            $authorization = "Authorization: Bearer {$this->apiKey}";
            curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorization]);
        } else {
            $neo_api = "{$options['protocol']}://{$this->ApiAuth()}@{$url}/{$endpoint}";
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $neo_api);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);

        // Handle Curl error codes
        // https://curl.se/libcurl/c/libcurl-errors.html
        if($errorNo === 3) throw new \Exception(sprintf('Malformed URL: %s', $neo_api)); // Catch errors caused by un-encoded values in request URLs
        if($errorNo !== 0) throw new \Exception(sprintf('Error performing API call %s', $neo_api)); // Catch all other errors

        return json_decode($result);
    }

    /**
     * Gets either the api key, or username:password
     * depending on what was provided in the class constructor.
     *
     * @return string
     */
    private function ApiAuth()
    {
        if (isset($this->apiKey)) {
            return $this->apiKey;
        } elseif (isset($this->username) && isset($this->password)) {
            return implode(':', [urlencode($this->username), urlencode($this->password)]);
        } else {
            throw new \Exception('No credentials provided. API key, or username/password required. Refer to the docs for more information');
        }
    }

    /**
     * Parse a URL into usable parts.
     *
     * @param string $url
     *
     * @return array
     */
    private function parseUrl(string $url)
    {
        $re = '/^(?<protocol>https?):\/\/((?<subdomain>.+)\.)?(?<domain>.+)\.(?<tld>[a-zA-Z]{2,})$/m';

        preg_match($re, $url, $matches);

        return [
            'protocol' => isset($matches['protocol']) ? $matches['protocol'] : 'http://',
            'subdomain' => $matches['subdomain'],
            'domain' => $matches['domain'],
            'tld' => $matches['tld'],
            'url' => $url,
        ];
    }
}
