<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Utils;

/**
 * Class Browser.
 */
class Browser
{
    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $platform;

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(string $userAgent)
    {
        $bname = 'Unknown';
        $platform = 'Unknown';

        // First get the platform
        if (false !== stripos($userAgent, 'linux')) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        }

        // Next get the name of the useragent
        if (false !== stripos($userAgent, 'MSIE') && !false !== stripos($userAgent, 'Opera')) {
            $bname = 'Internet Explorer';
            $userAgentBrand = 'MSIE';
        } elseif (false !== stripos($userAgent, 'Firefox')) {
            $bname = 'Mozilla Firefox';
            $userAgentBrand = 'Firefox';
        } elseif (false !== stripos($userAgent, 'Chrome')) {
            $bname = 'Google Chrome';
            $userAgentBrand = 'Chrome';
        } elseif (false !== stripos($userAgent, 'Safari')) {
            $bname = 'Apple Safari';
            $userAgentBrand = 'Safari';
        } elseif (false !== stripos($userAgent, 'Opera')) {
            $bname = 'Opera';
            $userAgentBrand = 'Opera';
        } elseif (false !== stripos($userAgent, 'Netscape')) {
            $bname = 'Netscape';
            $userAgentBrand = 'Netscape';
        }

        // finally get the correct version number
        $known = [
            'Version',
            $userAgentBrand,
            'other',
        ];
        $matches = null;
        $pattern = '#(?<browser>'.join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $userAgent, $matches);

        // see how many we have
        $i = count($matches['browser']);
        if (1 != $i) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($userAgent, 'Version') < strripos($userAgent, $userAgentBrand)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if (null == $version || '' == $version) {
            $version = '?';
        }

        $this->userAgent = $userAgent;
        $this->name = $bname;
        $this->version = $version;
        $this->platform = $platform;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return "{$this->name} - ({$this->version})";
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @return mixed
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
