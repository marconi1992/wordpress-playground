<?php

/*
Plugin Name: News API
Description: Import external articles from News API
Version: 1.0.0
Author: Felipe Guizar
*/

class News_Sync_Command {
    public function __invoke($args) {
		$url = 'http://newsapi:3000/news';
		$result = wp_remote_request($url);

		if (is_wp_error($result)) {
			WP_CLI::error('Error getting articles from ' . $url);
		}

		if (($status_code = $result['response']['code']) != 200) {
			WP_CLI::error('Got ' . $status_code . ' from ' . $url);
		}

		$articles = json_decode($result['body'], 1);

		if ($articles == false) {
			WP_CLI::error('Response body from ' . $url . ' is not json formatted');
		}

		$plugin_instance = News_Plugin::getInstance();
		$post_type = $plugin_instance->news_article_id;

		foreach ($articles as $article) {
			$title = $article['title'];
			$id = $article['id'];

			$post_id = wp_insert_post(array(
				'ID' => $id,
				'post_title' => $title,
				'post_type' => $post_type
			), true);

			$external_url = $article['url'];

			$plugin_instance->set_news_article_meta_box($post_id, $plugin_instance->external_url_id, $external_url);
		}
    }
}

class News_Plugin {

	static $instance;

	/**
	 * The plugin ID
	 * @access private
	 * @var string $plugin_name
	 */
	public $plugin_id= 'news';

	/**
	 * News article ID
	 * @access private
	 * @var string $news_article_id
	 */
	public $news_article_id;

	/**
	 * External URL meta ID
	 * @access private
	 * @var string $external_url_id
	 */
	public $external_url_id;

	private function __construct() {
		$this->news_article_id = $this->plugin_id . '_article';
		$this->external_url_id = $this->news_article_id . '-' . 'external_url';
		$this->register_news_article_post_type();

		add_action('save_post', array($this, 'save_news_article_meta_box'));
	}

	function register_news_article_post_type() {
		$article_post_type_args = array(
			'labels' => array(
				'name' => 'News Articles',
				'singular_name' => 'News Article'
			),
			'public' => true,
			'has_archive' => true,
			'description' => 'Articles from newsapi.org',
			'hierarchical' => true,
			'supports' => ['title'],
			'register_meta_box_cb' => array($this, 'register_news_article_meta_box'),
			'taxonomies' => []
		);

		register_post_type($this->news_article_id, $article_post_type_args);
	}

	function set_news_article_meta_box($post_id, $meta_box, $value) {
		update_post_meta($post_id, $meta_box, $value);
	}

	function save_news_article_meta_box() {
		if (isset($_POST['post_type']) && $_POST['post_type'] == $this->news_article_id) {
			$external_url_id = $this->external_url_id;
			$this->set_news_article_meta_box($_REQUEST['post_ID'], $external_url_id, $_REQUEST[$external_url_id]);
		}
	}

	function get_news_article_meta_box($post_id, $meta_box) {
		return get_post_meta($post_id, $meta_box);
	}

	function render_external_url_meta_box($post) {
		$external_url_id = $this->external_url_id;
		$external_url = get_post_meta($post->ID, $external_url_id, true);

		echo "<input style=\"width:100%;\" type=\"url\" name=\"$external_url_id\" value=\"$external_url\"";
	}

	function register_news_article_meta_box() {
		add_meta_box(
			$this->external_url_id,
			__('External URL', $this->external_url_id),
			array($this, 'render_external_url_meta_box'),
			$this->news_article_id,
			'normal'
		);
	}

	static function getInstance() {
		if (empty(self::$instance)) {
			return new self();
		}

		return self::$instance;
	}
}

add_action('init', function () {
	News_Plugin::getInstance();

	if (defined( 'WP_CLI' ) && WP_CLI) {
		$news_sync_command = new News_Sync_Command();
		WP_CLI::add_command('news sync', $news_sync_command);
	}
});
