<?php
/**
 * Files class
 * Locates plugin's files
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

/**
 * Files class
 */
class Files {

	/**
	 * Plugin file absolute path
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Plugin custom URL
	 *
	 * @var string
	 */
	protected $plugin_url = false;

	/**
	 * Plugin custom path
	 *
	 * @var string
	 */
	protected $plugin_path = false;

	/**
	 * Assets directory name with a slash at the end
	 *
	 * @var string
	 */
	protected $assets_dir_name;

	/**
	 * Class constructor
	 *
	 * @param string $plugin_file full path to main plugin file.
	 * @param mixed  $plugin_url  plugin custom url.
	 * @param mixed  $plugin_path plugin custom path.
	 */
	public function __construct( $plugin_file = '', $plugin_url = false, $plugin_path = false ) {
		$this->plugin_file     = $plugin_file;
		$this->plugin_url      = $plugin_url;
		$this->plugin_path     = $plugin_path;
		$this->assets_dir_name = array( 'assets', 'dist' );
	}

	/**
	 * Builds the dir name from an array of parts
	 *
	 * @uses   trainlingslashit()
	 * @param  array $parts parts of the path.
	 * @return string        dir name
	 */
	public function build_dir_from_array( $parts = array() ) {

		$dir = '';

		foreach ( $parts as $part ) {
			$dir .= trailingslashit( $part );
		}

		return $dir;

	}

	/**
	 * Resolves file path
	 * You can provide a file string or an array of dirs and file name at the end
	 *
	 * @param  mixed $file file structure.
	 * @return string       full file path
	 */
	public function resolve_file_path( $file = '' ) {

		if ( is_array( $file ) ) {
			$filename = array_pop( $file );
			$file     = $this->build_dir_from_array( $file ) . $filename;
		}

		return $file;

	}

	/**
	 * Gets the plugin root dir absolute path
	 *
	 * @return string path
	 */
	public function plugin_path() {
		return ! empty( $this->plugin_path ) ? $this->plugin_path : plugin_dir_path( $this->plugin_file );
	}

	/**
	 * Gets the plugin root dir url
	 *
	 * @return string url
	 */
	public function plugin_url() {
		return ! empty( $this->plugin_url ) ? $this->plugin_url : plugin_dir_url( $this->plugin_file );
	}

	/**
	 * Gets file path which is relative to plugin root path
	 *
	 * @param  mixed $file if it's an array, the dir structure will be built.
	 * @return string      file absolute path
	 */
	public function file_path( $file = '' ) {
		return $this->plugin_path() . $this->resolve_file_path( $file );
	}

	/**
	 * Gets file url which is relative to plugin root
	 *
	 * @param  mixed $file if it's an array, the dir structure will be built.
	 * @return string      file url
	 */
	public function file_url( $file = '' ) {
		return $this->plugin_url() . $this->resolve_file_path( $file );
	}

	/**
	 * Gets dir path which is relative to plugin root path
	 *
	 * @param  mixed $dir if it's an array, the dir structure will be built.
	 * @return string     dir absolute path
	 */
	public function dir_path( $dir = '' ) {
		return $this->plugin_path() . $this->build_dir_from_array( (array) $dir );
	}

	/**
	 * Gets dir url which is relative to plugin root
	 *
	 * @param  mixed $dir if it's an array, the dir structure will be built.
	 * @return string     dir url
	 */
	public function dir_url( $dir = '' ) {
		return $this->plugin_url() . $this->build_dir_from_array( (array) $dir );
	}

	/**
	 * Gets url to an asset file
	 *
	 * @param  string $type asset type - js | css | image.
	 * @param  string $file file name.
	 * @return string       asset file url
	 */
	public function asset_url( $type = '', $file = '' ) {
		$assets_dirs   = $this->assets_dir_name;
		$assets_dirs[] = $type;
		$assets_dirs[] = $file;
		return $this->file_url( $assets_dirs );
	}

	/**
	 * Gets path to an asset file
	 *
	 * @param  string $type asset type - js | css | images.
	 * @param  string $file file name.
	 * @return string       asset file path
	 */
	public function asset_path( $type = '', $file = '' ) {
		$assets_dirs   = $this->assets_dir_name;
		$assets_dirs[] = $type;
		$assets_dirs[] = $file;
		return $this->file_path( $assets_dirs );
	}

	/**
	 * Gets path to an asset file
	 *
	 * @param  string $type asset type - js | css | images.
	 * @param  string $file file name.
	 * @return string       asset file path
	 */
	public function asset_mtime( $type = '', $file = '' ) {
		return filemtime( $this->asset_path( $type, $file ) );
	}

	/**
	 * Encodes an image to base64
	 *
	 * @param  string $file image file name.
	 * @return string       base64 encoded image
	 */
	public function image_base64( $file = '' ) {
		$path = $this->asset_path( 'images', $file );
		$type = pathinfo( $path, PATHINFO_EXTENSION );
		// SVG mime type fix.
		if ( 'svg' === $type ) {
			$type = 'svg+xml';
		}
		$data = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		return 'data:image/' . $type . ';base64,' . base64_encode( $data );
	}

	/**
	 * Gets url to a vendor asset file
	 *
	 * @param  string $vendor asset vendor name (name of the vendor dir).
	 * @param  string $file   file name.
	 * @return string         asset file url
	 */
	public function vendor_asset_url( $vendor = '', $file = '' ) {
		$assets_dirs = array(
			'assets',
			'vendor',
			$vendor,
			$file,
		);
		return $this->file_url( $assets_dirs );
	}

}
