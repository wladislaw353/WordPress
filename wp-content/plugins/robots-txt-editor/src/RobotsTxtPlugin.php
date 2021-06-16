<?php namespace RobotsTxt;

use Premmerce\SDK\V2\FileManager\FileManager;
use RobotsTxt\Admin\Admin;
use RobotsTxt\Frontend\Frontend;

/**
 * Class RobotsTxtPlugin
 *
 * @package RobotsTxt
 */
class RobotsTxtPlugin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * RobotsTxtPlugin constructor.
	 *
     * @param string $mainFile
	 */
    public function __construct($mainFile) {
        $this->fileManager = new FileManager($mainFile);

        add_action('plugins_loaded', [ $this, 'loadTextDomain' ]);

	}

	/**
	 * Run plugin part
	 */
	public function run() {
		if ( is_admin() ) {
			new Admin( $this->fileManager );
		} else {
			new Frontend( $this->fileManager );
		}

	}

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('robots-txt-editor', false, $name . '/languages/');
    }

	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		// TODO: Implement activate() method.
        if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/robots.txt")){
            rename ( "{$_SERVER['DOCUMENT_ROOT']}/robots.txt" , "{$_SERVER['DOCUMENT_ROOT']}/robots_old.txt" );
        }


	}

	/**
	 * Fired when the plugin is deactivated
	 */
	public function deactivate() {
		// TODO: Implement deactivate() method.
        delete_option(Admin::OPTIONS);
        if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/robots_old.txt")){
            rename ( "{$_SERVER['DOCUMENT_ROOT']}/robots_old.txt" , "{$_SERVER['DOCUMENT_ROOT']}/robots.txt" );
        }
	}

	/**
	 * Fired during plugin uninstall
	 */
	public static function uninstall() {
		// TODO: Implement uninstall() method.
        delete_option(Admin::OPTIONS);
	}
}