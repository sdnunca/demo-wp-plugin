<?php
/**
 * Singleton trait
 *
 * @package  sdn-demo
 */

namespace DemoWPPlugin;

/**
 * Abstract class
 */
trait Singleton {
	protected static self $instance;

	/**
	 * Return instance of class
	 *
	 * @return self
	 */
	public static function instance() {
		if ( empty( static::$instance ) ) {
			$class            = get_called_class();
			static::$instance = new $class();
			if ( method_exists( static::$instance, 'setup' ) ) {
				static::$instance->setup();
			}
		}

		return static::$instance;
	}
}
