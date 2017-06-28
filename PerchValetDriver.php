<?php

class PerchValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request. If it does set
     * server name before returning true, otherwise return false
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        if(is_dir($sitePath.'/perch'))
        {
            $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
            return true;
        }

        return false;
        
    }
    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        

        if (file_exists($staticFilePath = $sitePath.'/public'.$uri)) {
            return $staticFilePath;
        } elseif ($this->isActualFile($staticFilePath = $sitePath.$uri)) {
            return $staticFilePath;
        }
        
        return false;
    }
    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        
        $dynamicCandidates = [
            $this->asActualFile($sitePath, $uri),
            $this->asPhpIndexFileInDirectory($sitePath, $uri),
            
        ];
        
        foreach ($dynamicCandidates as $candidate) {
            if ($this->isActualFile($candidate)) {
                $_SERVER['SCRIPT_FILENAME'] = $candidate;
                $_SERVER['SCRIPT_NAME'] = str_replace($sitePath, '', $candidate);
                $_SERVER['DOCUMENT_ROOT'] = $sitePath;
                return $candidate;
            }
        }
        
        
    }
    /**
     * Concatenate the site path and URI as a single file name.
     *
     * @param  string  $sitePath
     * @param  string  $uri
     * @return string
     */
    protected function asActualFile($sitePath, $uri)
    {
       
        return $sitePath.$uri;
    }
    /**
     * Format the site path and URI with a trailing "index.php".
     *
     * @param  string  $sitePath
     * @param  string  $uri
     * @return string
     */
    protected function asPhpIndexFileInDirectory($sitePath, $uri)
    {
        
        return $sitePath.rtrim($uri, '/').'/index.php';
    }
}
