<?php
/**
 * Build Schema script
 *
 * @package storefinder
 * @subpackage build
 */

error_reporting(E_ERROR);

$modelRoot = 'model/visioncart/';

echo ' <pre>';
if ($handle = opendir($modelRoot)) {
    echo "<h2>Removing old model:</h2>";

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
    	if (substr($file, 0, 1) != '.' && $file != 'remove.php') {
	    	$result = @unlink($modelRoot.$file);
	    	
	    	ob_start();
	    		var_dump($result);
	    	$content = ob_get_contents();
	    	$content=str_replace("\n", "", $content);
	    	ob_end_clean();
	    	
	        echo '('.$content.') - '.$file.'  <br />';
    	}
    }
    
    closedir($handle);
    
}

if ($handle = opendir($modelRoot.'mysql')) {

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
    	if (substr($file, 0, 1) != '.' && $file != 'remove.php') {
	    	$result = @unlink($modelRoot.'mysql/'.$file);
	    	
	    	ob_start();
	    		var_dump($result);
	    	$content = ob_get_contents();
	    	$content=str_replace("\n", "", $content);
	    	ob_end_clean();
	    	
	        echo '('.$content.') - mysql/'.$file.'  <br />';
    	}
    }
    
    closedir($handle);
    
}

echo '<h2>Creating model:</h2>';

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

umask(0000);

require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'core' => $root.'core/components/visioncart/',
    'model' => $root.'_build/model/',
    'assets' => $root.'assets/components/visioncart/',
    'schema' => $root.'_build/schema/',
);

$manager= $modx->getManager();
$generator= $manager->getGenerator();

$generator->parseSchema($sources['schema'].'build.mysql.schema.xml', $sources['model']);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}\n";

exit ();