<?php namespace RobotsTxt\Frontend;

use Premmerce\SDK\V2\FileManager\FileManager;
use RobotsTxt\Admin\Admin;

/**
 * Class Frontend
 *
 * @package RobotsTxt\Frontend
 */
class Frontend {


	/**
	 * @var FileManager
	 */
	private $fileManager;

	private $options;

	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;
        $this->options = get_option(Admin::OPTIONS);
        $this->registerActions();
	}

    public function registerActions()
    {

        add_filter( 'robots_txt', array($this, 'doRobots' ) );
    }


    public function doRobots($text)
    {
        $text = $this->options['robotsTxt'];
        return $text;


    }

}