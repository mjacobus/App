<?php

require_once 'WideImage/WideImage.php';

/**
 *
 *
 * @author marcelo.jacobus
 */
class App_Image
{

    /**
     * @var string
     */
    protected $_originalPath;
    /**
     * @var string
     */
    protected $_resizedPath;
    /**
     * @var string
     */
    protected $_tokenSalt;
    /**
     * @var string
     */
    protected $_token;
    /**
     * @var string
     */
    protected $_file;
    /**
     * @var string
     */
    protected $_fileExtention = 'jpg';
    /**
     * @var string
     */
    protected $_resizedFile;
    /**
     * @var int
     */
    protected $_width = 100;
    /**
     * @var int
     */
    protected $_height = 100;
    /**
     * @var mixed
     */
    protected $_background = false;
    /**
     * @var App_Image
     */
    public static $_instance;

    /**
     * Singleton pattern
     * @return App_Image
     */
    public static function getInstance()
    {
        if (App_Image::$_instance == null) {
            App_Image::$_instance = new self();
        }
        return App_Image::$_instance;
    }

    /**
     * Set the path where the original (unresized) images are stored
     * @param string $path
     * @return App_Image
     */
    public function setOriginalPath($path)
    {
        if ($this->folderExistsAndIsWritable($path)) {
            $this->_originalPath = $path;
        }
        return $this;
    }

    /**
     * Get the path where the resized images are stored
     * @return string
     */
    public function getOriginalPath()
    {
        if ($this->_originalPath !== null) {
            return $this->_originalPath;
        }
        throw new Exception("The original path was not defined.");
    }

    /**
     * Set the path where the resized images are stored
     * @param string $path
     * @return App_Image
     */
    public function setResizedPath($path)
    {
        if ($this->folderExistsAndIsWritable($path)) {
            $this->_resizedPath = $path;
        }
        return $this;
    }

    /**
     * Get the path where the resized images are stored
     * @return string
     */
    public function getResizedPath()
    {
        if ($this->_resizedPath !== null) {
            return $this->_resizedPath;
        }
        throw new Exception("The resized path was not defined.");
    }

    /**
     * Check whether given folder exists and is writable
     * @param string $folder
     * @return boolean true when file exists
     * @throws Exception when either not exist nor is writable
     */
    public function folderExistsAndIsWritable($folder)
    {
        if (!file_exists($folder)) {
            throw new Exception("Foder $folder does not exist.");
        }

        if (!is_writable($folder)) {
            throw new Exception("Foder $folder is not writable.");
        }
        return true;
    }

    /**
     * Set Width
     * @param int $width
     * @return App_Image
     */
    public function setWidth($width)
    {
        $this->_width = (int) $width;
        return $this;
    }

    /**
     * Get Width
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Set Width
     * @param int $width
     * @return App_Image
     */
    public function setHeight($height)
    {
        $this->_height = (int) $height;
        return $this;
    }

    /**
     * Set background
     * @return App_Image
     */
    public function setBackground($background)
    {
        $this->_background = $background;
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getBackground()
    {
        return $this->_background;
    }

    /**
     * Get Height
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Set file to be resized/displayed
     * @param string $width
     * @return App_Image
     */
    public function setFile($file)
    {
        $parts = explode('.', $file);
        if (count($parts)) {
            $this->setFileExtention(array_pop($parts));
            $this->_file = strtolower(implode('.', $parts));
            return $this;
        }
        throw new Exception("File with no extention given: '$file'");
    }

    /**
     * Get absolute path and file
     * @param bool $absolutePath
     * @return string
     */
    public function getFile($absolutePath = true)
    {
        $file = $this->_file . '.' . $this->getFileExtention();

        if ($absolutePath) {
            $subdir = $this->getDirname($file);
            $file = $this->getOriginalPath() . "/$subdir/$file";
        }
        return $file;
    }

    /**
     * Get resized file
     * @param bool $absolutePath
     * @return string
     */
    public function getResizedFile($absolutePath = true)
    {
        $file = $this->getFile(false);

        if ($absolutePath) {
            $sizeDir = $this->getWidth() . 'x' . $this->getHeight();
            $subdir = $this->getDirname($file);
            $file = $this->getResizedPath() . "/$sizeDir/$subdir/$file";
        }

        return $file;
    }

    /**
     * Set token salt
     * The salt is an extra layer of security, to avoid unalthorized requests
     * to resize pictures rence overloading/fulling the filesystem
     * @param string $width
     * @return App_Image
     */
    public function setTokenSalt($salt)
    {
        $this->_tokenSalt = $salt;
        return $this;
    }

    /**
     * Get token salt
     * The salt is an extra layer of security, to avoid unalthorized requests
     * to resize pictures rence overloading/fulling the filesystem
     * @return App_Image
     */
    public function getTokenSalt()
    {
        return $this->_tokenSalt;
    }

    /**
     *
     * @return binary
     */
    public function getFileContent()
    {
        if (file_exists($this->getFile())) {

            if (!$this->resizedExists()) {
                $this->resize();
            }

            $file = $this->getResizedFile();

            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }

        throw new App_Exception(sprintf('File "%s" do not exist', $this->getFile()));
    }

    /**
     * Checks whether a resized file aready exist
     * @return bool
     */
    public function resizedExists()
    {
        return file_exists($this->getResizedFile());
    }

    /**
     * Resize the image
     * @return App_Image
     */
    public function resize()
    {
        if ($this->getToken() !== $this->getValidToken()) {
            throw new Exception('Image Token do not match.');
        }

        $original = $this->getFile();
        $resized = $this->getResizedFile();
        $width = $this->getWidth();
        $height = $this->getHeight();

        $image = WideImage::load($original);

        //$image->setTransparentColor(0xffffff);
        $color = $image->getTransparentColor();

        if (true) {
            $resized = $image->resize($width, $height, 'inside')
                    ->resizeCanvas($width, $height, 'center', 'center', $color, 'up')
                    ->crop('center', 'center', $width, $height);
        } else {
            $resized = $image->resize($width, $height, 'inside');
        }

        $this->mkdir(dirname($this->getResizedFile()));
        $resized->saveToFile($this->getResizedFile());

        return $this;
    }

    /**
     * Get security token for allowing resizing
     * @return string
     */
    public function getValidToken()
    {
        $salt = $this->getTokenSalt();
        $token = $salt . $this->getResizedFile() . $salt;
        return sha1($token);
    }

    /**
     * Set user given token
     * @return string
     */
    public function setToken($token)
    {
        $this->_token = $token;
    }

    /**
     * Get user given token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Get a valid request
     * ie: ?file=$file&token=$token&width=$width&height=$height
     * @return string for resizing a image
     */
    public function getRequest()
    {
        $file = $this->getFile(false);
        $width = $this->getWidth();
        $height = $this->getHeight();
        $token = $this->getValidToken();

        $parts = explode('.', $file);
        $extention = array_pop($parts);
        $file = implode('.', $parts);

        return "/{$file}_{$width}x{$height}.{$extention}?token=$token";
    }

    /**
     * Get mime type
     * @return string
     */
    public function getFileContentType()
    {
        return 'image/' . $this->getFileExtention();
    }

    /**
     * Set file extention
     * @param string $extention
     * @return App_Image
     */
    public function setFileExtention($extention)
    {
        $this->_fileExtention = strtolower($extention);
        return $this;
    }

    /**
     * Get file Extention
     * @return string
     */
    public function getFileExtention()
    {
        return $this->_fileExtention;
    }

    /**
     * Save given file to a new location within the App_Image file structure
     * @param string $image
     * @param bool $deleteOriginal
     * @return string 
     */
    public function saveImage($image, $deleteOriginal = true)
    {
        if (file_exists($image)) {
            $imageParts = explode('.', $image);
            $extention = array_pop($imageParts);

            $filename = $this->strToHex(md5_file($image)) . '.' . strtolower($extention);

            $path = $this->getOriginalPath() . '/' . $this->getDirname($filename);

            $this->mkdir($path);
            $saveTo = $path . '/' . $filename;

            WideImage::load($image)->saveToFile($saveTo);

            if ($deleteOriginal) {
                unlink($image);
            }

            return $filename;
        }

        throw new App_Exception(sprintf('file "%s" not found', $image));
    }

    /**
     * Convert to hexadecimal
     * @param string $string
     * @return string
     */
    function strToHex($string)
    {
        $hex = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * Get the dirname for a given filename
     * @param string $filename
     * @return string
     */
    public function getDirname($filename)
    {
        if (preg_match('/(([a-f0-9]{1})([a-f0-9]{2})([a-f0-9]{2})[a-f0-9]{28,})\.\w{3,4}/', $filename, $matches)) {

            $path = implode(DIRECTORY_SEPARATOR, array(
                    $matches[2],
                    $matches[3],
                    $matches[4],
                ));

            return $path;
        } else {
            return dirname($filename);
        }
    }

    /**
     * Mkdir recursive
     * @param string $dirname
     * @return App_Image
     */
    public function mkdir($dirname)
    {
        if (!file_exists($dirname)) {
            if (!mkdir($dirname, 0770, true)) {
                throw new App_Exception(sprintf('Failed to make direcotry "%s"', $dirname));
            }
        }
        return $this;
    }

    /**
     * Remove the image and its resized versions
     * @param string $filename
     */
    public function removeImage($filename)
    {
        if (preg_match('/[a-f0-9]{32,}\.\w{3,4}/', $filename, $matches)) {
            $subdir = $this->getDirname($filename);
            $original = $this->getOriginalPath() . "/$subdir/$filename";

            if (file_exists($original)) {
                unlink($original);
            }

            $resizedPath = $this->getResizedPath();
            $dh = opendir($resizedPath);

            while (false !== ($sizeFolder = readdir($dh))) {

                if (!in_array($sizeFolder, array('.', '..'))) {
                    $file = "$resizedPath/$sizeFolder/$subdir/$filename";

                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
            closedir($dh);
        } else {
            unlink($filename);
        }
        return $this;
    }

}
