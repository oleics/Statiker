<?php/** * @author Oliver Leics */class StatikerBuilder {        const WET = true;    const OVERWITE = false;        /**     * Removes all empty folders inside a directory.     *      * @param string $path     * @param boolean|integer $lvl     * @return boolean     */    private static function RemoveEmptySubFolders($path, $lvl=false) {        $empty=true;        foreach(glob($path.DIRECTORY_SEPARATOR.'*') as $file) {            $empty &= is_dir($file) && self::RemoveEmptySubFolders($file, true);        }        return $lvl ? ($empty && rmdir($path)) : $empty;    }        /**     * Construct a new StatikerBuilder instance.     *      * @param object $modx A instance of modX.     */    public function __construct(&$modx) {        $this->modx =& $modx;    }        /**     * Builds a static file for a resource     *      * @param object $resource An instance of modResource.     * @return array|boolean     */    public function build(&$resource) {        return $this->writeStaticFile($resource, null, self::WET, self::OVERWITE);    }        /**     * Remove the static file of a resource-id.     *      * @param integer $resourceId     * @return boolean|object     */    public function removeFile($resourceId) {        $modx =& $this->modx;        $statikerFile = $modx->getObject('statikerFile', $resourceId);        if($statikerFile) {            if($statikerFile->remove() == false) {                return false;            }            $static_path = $statikerFile->get('static_path');            if(!empty($static_path)) {                @unlink($static_path);                @unlink($static_path.'.gz');                $site = $modx->getObject('statikerSite', array('context_key' => $statikerFile->get('context_key')));                if($site) {                    self::RemoveEmptySubFolders($site->get('write_to_directory'));                }            }            return $statikerFile;        } else {            return false;        }    }        /**     *      *      * @param object $resource An instance of modResource.     * @return boolean     */    private function deleteStaticFile(&$resource) {        $modx =& $this->modx;        $statikerFile = $modx->getObject('statikerFile', $resource->get('id'));        if($statikerFile) {            $old_static_path = $statikerFile->get('static_path');            if(!empty($old_static_path)) {                @unlink($old_static_path);                @unlink($old_static_path.'.gz');                $modx->log(modX::LOG_LEVEL_INFO, 'Removed: '.$old_static_path);            }            $statikerFile->remove();            return true;        } else {            return false;        }    }        /*  */    private function writeStaticFile(&$resource, $context=null, $wet=false, $overwrite=false) {            $modx =& $this->modx;                //         $site = $modx->getObject('statikerSite', array('context_key' => $resource->get('context_key')));        if(!$site) {            return false;        }                //         $write_to_directory = $site->get('write_to_directory');                //         if(empty($context)) {            $context = $modx->getContext($resource->get('context_key'));            $context->prepare();        }                // Theoretical URI:        // $resource->cleanAlias($scriptProperties['pagetitle']);        // $resource->getAliasPath($this->get('alias'));        // $path = $modx->makeUrl($resource->get('id'));                //         $tvs = array(            'statiker.build' => false,            'statiker.compress' => false,            'statiker.gzencode' => false,            'statiker.overwrite' => false,        );        foreach($resource->getMany('TemplateVars', array('name:IN'=>array_keys($tvs))) as $tv) {            $tvs[$tv->get('name')] = (bool)$tv->renderOutput($resource->get('id'));         }                //         $friendly_urls = $modx->getOption('friendly_urls', $context->config, false);        $site_url = $modx->getOption('site_url', $context->config, false);        $farm_url = $modx->getOption('statiker.farm_url', $context->config, $modx->getOption('baseUrl', $modx->statiker->config).'farm/');                //         $static_path = null;        $static_url = null;        $static_size = -1;        $static_size_compressed = -1;        $static_size_gzencoded = -1;        $bytes_written = -1;        $bytes_written_gzencoded = -1;                //         #$modx->log(modX::LOG_LEVEL_INFO, 'site_url: '.$modx->getOption('site_url', $context->config));        #$modx->log(modX::LOG_LEVEL_INFO, 'makeUrl: '.$modx->makeUrl($resource->get('id')));        #$modx->log(modX::LOG_LEVEL_INFO, ''.$this->getStaticPath($resource, $context));                //         if(            $friendly_urls&&$site_url&&$write_to_directory            &&is_dir($write_to_directory)&&is_writable($write_to_directory)            &&$tvs['statiker.build']&&$resource->get('published')        ) {                    //             $static_url = $modx->makeUrl($resource->get('id'), $context->get('key'), '', 'full');                        //             $static_path = $this->getStaticPath($resource, $context);            $static_path = $write_to_directory.$static_path;                        // $static_path must be a statikerFile to allow override            $statikerFile = $modx->getObject('statikerFile', array('static_path' => $static_path));            if(file_exists($static_path)) {                if(!$statikerFile) {                    $modx->log(modX::LOG_LEVEL_ERROR, 'File exists, but is not a statikerFile: '.$static_path);                    return false;                }            }            // conflict: two resources collide            if($statikerFile&&$statikerFile->get('resource')!=$resource->get('id')) {                $modx->log(modX::LOG_LEVEL_ERROR, 'File is a statikerFile, but its resource is: '.$statikerFile->get('resource'));                return false;            }            unset($statikerFile);                                    //             if($tvs['statiker.overwrite'] || ! file_exists($static_path)) {                // fetch contents                $farm_url = $farm_url.'?ctx='.$context->get('key').'&id='.$resource->get('id');                list($status, $content_type, $headers, $contents) =                    $this->__http_request(                        $farm_url,                        'GET',                        '',                        array('modAuth: '.$modx->site_id)                );                                // status must be 200                if($status!=200) {                    $modx->log(modX::LOG_LEVEL_ERROR, 'Got a '.$status.' from '.$farm_url);                    return false;                }                                //                 $static_size = strlen($contents);                                // compress contents                if($tvs['statiker.compress']) {                    $resource_content_type = $resource->getOne('ContentType');                    if($resource_content_type) {                        require_once($modx->statiker->config['corePath'].'lib/lib.compressor.php');                        $compressor = new Compressor();                        $t = array_pop(explode('/', $resource_content_type->get('mime_type'), 2));                        if(method_exists($compressor, $t)) {                            $contents = $compressor->{$t}($contents);                            $static_size_compressed = strlen($contents);                        }                    }                }                                // write contents                $modx->log(modX::LOG_LEVEL_INFO, $static_path);                if($wet) {                    $bytes_written = $this->saveFile($static_path, $contents);                    if($tvs['statiker.gzencode']) {                        $contents = gzencode($contents, 9);                        $static_size_gzencoded = strlen($contents);                        $bytes_written_gzencoded = $this->saveFile($static_path.'.gz', $contents);                    }                }                unset($contents);                                // write result                $statikerFile = $modx->getObject('statikerFile', $resource->get('id'));                if($statikerFile) {                    $old_static_path = $statikerFile->get('static_path');                    if(!empty($old_static_path)&&$old_static_path!=$static_path) {                        @unlink($old_static_path);                        @unlink($old_static_path.'.gz');                    }                } else {                    $statikerFile = $modx->newObject('statikerFile');                }                $statikerFile->fromArray(array(                    'resource' => $resource->get('id'),                    'context_key' => $resource->get('context_key'),                    'static_path' =>$static_path,                    'static_url' => $static_url,                    'static_size' => $static_size,                    'static_size_compressed' => $static_size_compressed,                    'static_size_gzencoded' => $static_size_gzencoded,                    'bytes_written' => $bytes_written,                    'bytes_written_gzencoded' => $bytes_written_gzencoded                ), '', true);                $statikerFile->save();                            }        } else {            $modx->log(modX::LOG_LEVEL_INFO, 'Skipped: '.$resource->get('id'));            $this->deleteStaticFile($resource);        }                //         return array(            'resource' => $resource->get('id'),            'context_key' => $context->get('key'),            'static_path' => $static_path,            'static_url' => $static_url,            'static_size' => $static_size,            'static_size_compressed' => $static_size_compressed,            'static_size_gzencoded' => $static_size_gzencoded,            'bytes_written' => $bytes_written,            'bytes_written_gzencoded' => $bytes_written_gzencoded        );    }        /* generates the path of a static file */    private function getStaticPath(&$resource, &$context) {        $modx =& $this->modx;        $path = $modx->makeUrl($resource->get('id'), $context->get('key'), '', 'full');        #$path = $context->makeUrl($resource->get('id'), '', 'full');        $path = array_pop(explode($modx->getOption('site_url', $context->config), $path, 2));        if(            $resource->get('id')==$modx->getOption('site_start', $context->config)            || $resource->get('isfolder')        ) {            $path .= 'index.html';        }        return $path;    }        /* writes $contents to $path, recursivly creates direcories. */    private function saveFile($path, $contents) {        if(!is_dir(dirname($path))) {            mkdir(dirname($path), 0755, true);        }        return file_put_contents($path, $contents);    }        /*  */    private function __http_request($url, $method='GET', $post_data='', $head=array()) {        // create a new curl resource        #$head = array();        $ch = curl_init($url);        // set URL and other appropriate options        curl_setopt($ch, CURLOPT_URL, $url);        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        curl_setopt($ch, CURLOPT_HEADER, true);        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        curl_setopt($ch, CURLOPT_ENCODING, '');        #curl_setopt($ch, CURLOPT_USERAGENT, 'PHP/'.phpversion());        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.73 [de]C-CCK-MCD DT (Win98; U)');        // grab URL        $contents = curl_exec( $ch);        list($headers, $contents) = explode("\r\n\r\n", $contents, 2);        #var_dump($headers);        #var_dump(curl_getinfo($ch));        #var_dump(htmlspecialchars($contents));        #$contents = encode( $this -> html_entity_decode( $contents), CHARSET);        $errno = curl_errno($ch);        if($errno) {            $error = curl_error($ch);            trigger_error($error, E_USER_WARNING);            return false;        }        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);        $content_type = array_shift(explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE)));        //$this->status = $status;        //$this->content_type = $content_type;        // HACK: Remove NS from XML to make it easyer for SimpleXML..        #$contents = str_replace(' xmlns="http://api.spreadshirt.net"', '', $contents);        return array($status, $content_type, $headers, $contents);    }}