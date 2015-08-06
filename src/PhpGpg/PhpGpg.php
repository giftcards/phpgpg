<?php
namespace PhpGpg;

use PhpGpg\Driver\DriverInterface;

class PhpGpg
{
    const SIG_MODE_CLEAR  = 1;//wraps the clear text in a gpg message with signature embedded
    const SIG_MODE_NORMAL = 2;//creates binary data with signature embedded
    const SIG_MODE_DETACH = 3;//only the signature is returned

    const ERROR_MODE_SILENT = 1;
    const ERROR_MODE_WARNING = 2;
    const ERROR_MODE_EXCEPTION = 3;

    private $_driver = null;
    private $error_mode = self::ERROR_MODE_SILENT;

    public static $default_driver = null;

    public static $default_error_mode = self::ERROR_MODE_SILENT;

    public function __construct($homedir = null, DriverInterface $driver = null, $options = array())
    {
        if ($driver === null && self::$default_driver === null) {
            if (extension_loaded('gnupg')) {
                $driver = new Driver\GnuPG\GpgMe($homedir, $options);
            } else {
                $driver = new Driver\GnuPG\Cli($homedir, $options);
            }
        } elseif ($driver === null && self::$default_driver !== null) {
            if (self::$default_driver instanceof DriverInterface) {
                $driver = self::$default_driver;
            } elseif (is_string(self::$default_driver)) {
                $driver = new self::$default_driver($homedir, $options);
            }
        }
        $this->_driver = $driver;
    }

    public static function setDefaultDriver($driver)
    {
        self::$default_driver = $driver;
    }

    public function setErrorMode($mode)
    {
        $this->error_mode = $mode;
        $this->_driver->setErrorMode($mode);
    }

    public function getErrorMode()
    {
        return $this->error_mode;
    }

    public function __call($name, $arguments)
    {
        return  call_user_func_array(array($this->_driver, $name), $arguments);
    }
}
