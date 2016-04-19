<?php
/**
 * H5P Plugin.
 *
 * @package   H5P
 * @author    Joubel <contact@joubel.com>
 * @license   MIT
 * @link      http://joubel.com
 * @copyright 2014 Joubel
 */

/**
 * H5P Content Admin class
 *
 * @package H5P_Plugin_Admin
 * @author Joubel <contact@joubel.com>
 */
class H5PContentAdmin
{

    const PATH_SCRIPTS = '/scripts/h5p/editor/';
    const PATH_STYLES = '/css/h5p/editor/';
    /**
     * @since 1.1.0
     */
    private $plugin_slug = null;

    /**
     * Editor instance
     *
     * @since 1.1.0
     * @var \H5peditor
     */
    protected static $h5peditor = null;

    /**
     * Keep track of the current content.
     *
     * @since 1.1.0
     */
    public $content = null;

    /**
     * Are we inserting H5P content on this page?
     *
     * @since 1.2.0
     */
    private $insertButton = false;

    /**
     * Initialize content admin and editor
     *
     * @since 1.1.0
     * @param string $plugin_slug
     */
    public function __construct($plugin_slug)
    {
        getWPDB();

        $this->plugin_slug = $plugin_slug;
    }

    /**
     * Load content and alter page title for certain pages.
     *
     * @since 1.1.0
     * @param string $page
     * @param string $admin_title
     * @param string $title
     * @return string
     */
    public function alter_title($page, $admin_title, $title)
    {
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_STRING);
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Find content title
        $show = ($page === 'h5p' && ($task === 'show' || $task === 'results'));
        $edit = ($page === 'h5p_new');
        if (($show || $edit) && $id !== null) {
            if ($this->content === null) {
                $this->load_content($id);
            }

            if (!is_string($this->content)) {
                if ($edit) {
                    $admin_title = str_replace($title, 'Edit', $admin_title);
                }
                $admin_title = esc_html($this->content['title']) . ' &lsaquo; ' . $admin_title;
            }
        }

        return $admin_title;
    }

    /**
     * Will load and set the content variable.
     * Also loads tags related to content.
     *
     * @since 1.6.0
     * @param int $id
     */
    public function load_content($id)
    {
        global $wpdb;
        $plugin = H5P_Plugin::get_instance();

        $this->content = $plugin->get_content($id);
        if (!is_string($this->content)) {
            $tags = $wpdb->get_results($wpdb->prepare(
                "SELECT t.name
             FROM h5p_contents_tags ct
             JOIN h5p_tags t ON ct.tag_id = t.id
            WHERE ct.content_id = %d",
                $id
            ));
            $this->content['tags'] = '';
            foreach ($tags as $tag) {
                $this->content['tags'] .= ($this->content['tags'] !== '' ? ', ' : '') . $tag->name;
            }
        }
    }

    /**
     * Permission check. Can the current user edit the given content?
     *
     * @since 1.1.0
     * @param array $content
     * @return boolean
     */
    private function current_user_can_edit($content)
    {
        if (current_user_can('edit_others_h5p_contents')) {
            return true;
        }

        $user_id = get_current_user_id();
        if (is_array($content)) {
            return ($user_id === (int)$content['user_id']);
        }

        return ($user_id === (int)$content->user_id);
    }

    /**
     * Permission check. Can the current user view results for the given content?
     *
     * @since 1.2.0
     * @param array $content
     * @return boolean
     */
    private function current_user_can_view_content_results($content)
    {
        if (!get_option('h5p_track_user', true)) {
            return false;
        }

        return $this->current_user_can_edit($content);
    }

    /**
     * Display a list of all h5p content.
     *
     * @since 1.1.0
     */
    public function display_contents_page()
    {
        switch (filter_input(INPUT_GET, 'task', FILTER_SANITIZE_STRING)) {
            case null:
                include_once('views/contents.php');

                $headers = array(
                    (object)array(
                        'text' => __('Title', $this->plugin_slug),
                        'sortable' => true
                    ),
                    (object)array(
                        'text' => __('Content type', $this->plugin_slug),
                        'sortable' => true,
                        'facet' => true
                    ),
                    (object)array(
                        'text' => __('Author', $this->plugin_slug),
                        'sortable' => true,
                        'facet' => true
                    ),
                    (object)array(
                        'text' => __('Tags', $this->plugin_slug),
                        'sortable' => false,
                        'facet' => true
                    ),
                    (object)array(
                        'text' => __('Last modified', $this->plugin_slug),
                        'sortable' => true
                    ),
                    (object)array(
                        'text' => __('ID', $this->plugin_slug),
                        'sortable' => true
                    )
                );
                if (get_option('h5p_track_user', true)) {
                    $headers[] = (object)array(
                        'class' => 'h5p-results-link'
                    );
                }
                $headers[] = (object)array(
                    'class' => 'h5p-edit-link'
                );

                $plugin_admin = H5P_Plugin_Admin::get_instance();
                $plugin_admin->print_data_view_settings(
                    'h5p-contents',
                    admin_url('admin-ajax.php?action=h5p_contents'),
                    $headers,
                    array(true),
                    __("No H5P content available. You must upload or create new content.", $this->plugin_slug),
                    (object)array(
                        'by' => 4,
                        'dir' => 0
                    )
                );

                return;

            case 'show':
                // Admin preview of H5P content.
                if (is_string($this->content)) {
                    H5P_Plugin_Admin::set_error($this->content);
                    H5P_Plugin_Admin::print_messages();
                } else {
                    $plugin = H5P_Plugin::get_instance();
                    $embed_code = $plugin->add_assets($this->content);
                    include_once('views/show-content.php');
                    H5P_Plugin::get_instance()->add_settings();

                    // Log view
                    new H5P_Event('content', null,
                        $this->content['id'],
                        $this->content['title'],
                        $this->content['library']['name'],
                        $this->content['library']['majorVersion'] . '.' . $this->content['library']['minorVersion']);
                }

                return;

            case 'results':
                // View content results
                if (is_string($this->content)) {
                    H5P_Plugin_Admin::set_error($this->content);
                    H5P_Plugin_Admin::print_messages();
                } else {
                    // Print HTML
                    include_once('views/content-results.php');
                    $plugin_admin = H5P_Plugin_Admin::get_instance();
                    $plugin_admin->print_data_view_settings(
                        'h5p-content-results',
                        admin_url('admin-ajax.php?action=h5p_content_results&id=' . $this->content['id']),
                        array(
                            (object)array(
                                'text' => __('User', $this->plugin_slug),
                                'sortable' => true
                            ),
                            (object)array(
                                'text' => __('Score', $this->plugin_slug),
                                'sortable' => true
                            ),
                            (object)array(
                                'text' => __('Maximum Score', $this->plugin_slug),
                                'sortable' => true
                            ),
                            (object)array(
                                'text' => __('Opened', $this->plugin_slug),
                                'sortable' => true
                            ),
                            (object)array(
                                'text' => __('Finished', $this->plugin_slug),
                                'sortable' => true
                            ),
                            __('Time spent', $this->plugin_slug)
                        ),
                        array(true),
                        __("There are no logged results for this content.", $this->plugin_slug),
                        (object)array(
                            'by' => 4,
                            'dir' => 0
                        )
                    );

                    // Log content result view
                    new H5P_Event('results', 'content',
                        $this->content['id'],
                        $this->content['title'],
                        $this->content['library']['name'],
                        $this->content['library']['majorVersion'] . '.' . $this->content['library']['minorVersion']);
                }

                return;
        }

        print '<div class="wrap"><h2>' . esc_html__('Unknown task.', $this->plugin_slug) . '</h2></div>';
    }

    /**
     * Handle form submit when uploading, deleteing or editing H5Ps.
     * TODO: Rename to process_content_form ?
     *
     * @since 1.1.0
     */
    public function process_new_content()
    {
        $plugin = H5P_Plugin::get_instance();

        // Check if we have any content or errors loading content
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id) {
            $this->load_content($id);
            if (is_string($this->content)) {
                H5P_Plugin_Admin::set_error($this->content);
                $this->content = null;
            }
        }

        if ($this->content !== null) {
            // We have existing content

            if (!$this->current_user_can_edit($this->content)) {
                // The user isn't allowed to edit this content
                H5P_Plugin_Admin::set_error(__('You are not allowed to edit this content.', $this->plugin_slug));

                return;
            }

            // Check if we're deleting content
            $delete = filter_input(INPUT_GET, 'delete');
            if ($delete) {
                if (wp_verify_nonce($delete, 'deleting_h5p_content')) {
                    $this->set_content_tags($this->content['id']);
                    $storage = $plugin->get_h5p_instance('storage');
                    $storage->deletePackage($this->content);

                    // Log content delete
                    new H5P_Event('content', 'delete',
                        $this->content['id'],
                        $this->content['title'],
                        $this->content['library']['name'],
                        $this->content['library']['majorVersion'] . '.' . $this->content['library']['minorVersion']);

                    wp_safe_redirect(admin_url('admin.php?page=h5p'));

                    return;
                }
                H5P_Plugin_Admin::set_error(__('Invalid confirmation code, not deleting.', $this->plugin_slug));
            }
        }

        // Check if we're uploading or creating content
        $action = filter_input(INPUT_POST, 'action', FILTER_VALIDATE_REGEXP,
            array('options' => array('regexp' => '/^(upload|create)$/')));
        if ($action) {
            check_admin_referer('h5p_content', 'yes_sir_will_do'); // Verify form
            $core = $plugin->get_h5p_instance('core'); // Make sure core is loaded

            $result = false;
            if ($action === 'create') {
                // Handle creation of new content.
                $result = $this->handle_content_creation($this->content);
            } elseif (isset($_FILES['h5p_file']) && $_FILES['h5p_file']['error'] === 0) {
                // Create new content if none exists
                $content = ($this->content === null ? array('disable' => H5PCore::DISABLE_NONE) : $this->content);
                $content['title'] = $this->get_input_title();
                $content['uploaded'] = true;
                $this->get_disabled_content_features($core, $content);

                // Handle file upload
                $plugin_admin = H5P_Plugin_Admin::get_instance();
                $result = $plugin_admin->handle_upload($content);
            }

            if ($result) {
                $content['id'] = $result;
                $this->set_content_tags($content['id'], filter_input(INPUT_POST, 'tags'));
                wp_safe_redirect(admin_url('admin.php?page=h5p&task=show&id=' . $result));
            }
        }
    }

    /**
     * Save tags for given content.
     * Removes unused tags.
     *
     * @param int $content_id
     * @param string $tags
     */
    public function set_content_tags($content_id, $tags = '')
    {
        global $wpdb;
        $tag_ids = array();

        // Create array and trim input
        $tags = explode(',', $tags);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag === '') {
                continue;
            }

            // Find out if tag exists and is linked to content
            $exists = $wpdb->get_row($wpdb->prepare(
                "SELECT t.id, ct.content_id
             FROM {$wpdb->prefix}h5p_tags t
        LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct ON ct.content_id = %d AND ct.tag_id = t.id
            WHERE t.name = %s",
                $content_id, $tag
            ));

            if (empty($exists)) {
                // Create tag
                $exists = array('name' => $tag);
                $wpdb->insert("{$wpdb->prefix}h5p_tags", $exists, array('%s'));
                $exists = (object)$exists;
                $exists->id = $wpdb->insert_id;
            }
            $tag_ids[] = $exists->id;

            if (empty($exists->content_id)) {
                // Connect to content
                $wpdb->insert("{$wpdb->prefix}h5p_contents_tags",
                    array('content_id' => $content_id, 'tag_id' => $exists->id), array('%d', '%d'));
            }
        }

        // Remove tags that are not connected to content (old tags)
        $and_where = empty($tag_ids) ? '' : " AND tag_id NOT IN (" . implode(',', $tag_ids) . ")";
        $wpdb->query("DELETE FROM {$wpdb->prefix}h5p_contents_tags WHERE content_id = {$content_id}{$and_where}");

        // Maintain tags table by remove unused tags
        $wpdb->query("DELETE t.* FROM {$wpdb->prefix}h5p_tags t LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct ON t.id = ct.tag_id WHERE ct.content_id IS NULL");
    }

    /**
     * Check to see if the installation has any libraries.
     *
     * @return bool
     */
    public function has_libraries()
    {
        $query = DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->from('h5p_libraries')
            ->where('runnable', '=', 1);

        return ((int)$query->query()->fetch(0)['counter'] > 0);
    }

    /**
     * Create new content.
     *
     * @since 1.1.0
     * @param array $content
     * @return mixed
     */
    public function handle_content_creation($content)
    {
        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');

        // Keep track of the old library and params
        $oldLibrary = null;
        $oldParams = null;
        if ($content !== null) {
            $oldLibrary = $content['library'];
            $oldParams = json_decode($content['params']);
        } else {
            $content = array(
                'disable' => H5PCore::DISABLE_NONE
            );
        }

        // Get library
        $content['library'] = $core->libraryFromString($this->get_input('library'));
        if (!$content['library']) {
            $core->h5pF->setErrorMessage(__('Invalid library.'));

            return false;
        }

        // Check if library exists.
        $content['library']['libraryId'] = $core->h5pF->getLibraryId($content['library']['machineName'],
            $content['library']['majorVersion'], $content['library']['minorVersion']);
        if (!$content['library']['libraryId']) {
            $core->h5pF->setErrorMessage(__('No such library.'));

            return false;
        }

        // Get title
        $content['title'] = $this->get_input_title();
        if ($content['title'] === null) {
            return false;
        }

        // Check parameters
        $content['params'] = $this->get_input('parameters');
        if ($content['params'] === null) {
            return false;
        }
        $params = json_decode($content['params']);
        if ($params === null) {
            $core->h5pF->setErrorMessage(__('Invalid parameters.'));

            return false;
        }

        // Set disabled features
        $this->get_disabled_content_features($core, $content);

        // Save new content
        $content['id'] = $core->saveContent($content);

        // Create content directory
        $editor = $this->get_h5peditor_instance();
        if (!$editor->createDirectories($content['id'])) {
            $core->h5pF->setErrorMessage(__('Unable to create content directory.'));
            // Remove content.
            $core->h5pF->deleteContentData($content['id']);

            return false;
        }

        // Move images and find all content dependencies
        $editor->processParameters($content['id'], $content['library'], $params, $oldLibrary, $oldParams);

        return $content['id'];
    }

    /**
     * Extract disabled content features from input post.
     *
     * @since 1.2.0
     * @param H5PCore $core
     * @param int $current
     * @return int
     */
    public function get_disabled_content_features($core, &$content)
    {
        $set = array(
            'frame' => filter_input(INPUT_POST, 'frame', FILTER_VALIDATE_BOOLEAN),
            'download' => filter_input(INPUT_POST, 'download', FILTER_VALIDATE_BOOLEAN),
            'embed' => filter_input(INPUT_POST, 'embed', FILTER_VALIDATE_BOOLEAN),
            'copyright' => filter_input(INPUT_POST, 'copyright', FILTER_VALIDATE_BOOLEAN),
        );
        $content['disable'] = $core->getDisable($set, $content['disable']);
    }

    /**
     * Get input post data field.
     *
     * @since 1.1.0
     * @param string $field The field to get data for.
     * @param string $default Optional default return.
     * @return string
     */
    private function get_input($field, $default = null)
    {
        // Get field
        $value = filter_input(INPUT_POST, $field);
        if ($value === null) {
            if ($default === null) {
                // No default, set error message.
                H5P_Plugin_Admin::set_error(sprintf(__('Missing %s.'), $field));
            }

            return $default;
        }

        return $value;
    }

    /**
     * Get input post data field title. Validates.
     *
     * @since 1.1.0
     * @return string
     */
    public function get_input_title()
    {
        $title = $this->get_input('title');
        if ($title === null) {
            return null;
        }

        // Trim title and check length
        $trimmed_title = trim($title);
        if ($trimmed_title === '') {
            H5P_Plugin_Admin::set_error(sprintf(__('Missing %s.'), 'title'));

            return null;
        }

        if (strlen($trimmed_title) > 255) {
            H5P_Plugin_Admin::set_error(__('Title is too long. Must be 256 letters or shorter.'));

            return null;
        }

        return $trimmed_title;
    }

    /**
     * Add custom media button for selecting H5P content.
     *
     * @since 1.1.0
     * @return string
     */
    public function add_insert_button()
    {
        $this->insertButton = true;

        $insert_method = get_option('h5p_insert_method', 'id');
        $button_content =
            '<a href="#" id="add-h5p" class="button" title="' . __('Insert H5P Content',
                $this->plugin_slug) . '" data-method="' . $insert_method . '">' .
            __('Add H5P') .
            '</a>';

        return $button_content;
    }

    /**
     * Adds scripts and settings for allowing selection of H5P contents when
     * inserting into pages, posts etc.
     *
     * @since 1.2.0
     */
    public function print_insert_content_scripts()
    {
        if (!$this->insertButton) {
            return;
        }

        $plugin_admin = H5P_Plugin_Admin::get_instance();
        $plugin_admin->print_data_view_settings(
            'h5p-insert-content',
            admin_url('admin-ajax.php?action=h5p_insert_content'),
            array(
                (object)array(
                    'text' => __('Title'),
                    'sortable' => true
                ),
                (object)array(
                    'text' => __('Content type'),
                    'sortable' => true,
                    'facet' => true
                ),
                (object)array(
                    'text' => __('Tags'),
                    'sortable' => false,
                    'facet' => true
                ),
                (object)array(
                    'text' => __('Last modified'),
                    'sortable' => true
                ),
                (object)array(
                    'class' => 'h5p-insert-link'
                )
            ),
            array(true),
            __("No H5P content available. You must upload or create new content."),
            (object)array(
                'by' => 3,
                'dir' => 0
            )
        );
    }

    /**
     * Log when content is inserted
     *
     * @since 1.6.0
     */
    public function ajax_inserted()
    {
        global $wpdb;

        $content_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$content_id) {
            return;
        }

        // Get content info for log
        $content = $wpdb->get_row($wpdb->prepare("
        SELECT c.title, l.name, l.major_version, l.minor_version
          FROM {$wpdb->prefix}h5p_contents c
          JOIN {$wpdb->prefix}h5p_libraries l ON l.id = c.library_id
         WHERE c.id = %d
        ", $content_id));

        // Log view
        new H5P_Event('content', 'shortcode insert',
            $content_id, $content->title,
            $content->name, $content->major_version . '.' . $content->minor_version);
    }

    /**
     * List content to choose from when inserting H5Ps.
     *
     * @since 1.2.0
     */
    public function ajax_insert_content()
    {
        $this->ajax_contents(true);
    }

    /**
     * Generic function for listing all H5P contents.
     *
     * @global \wpdb $wpdb
     * @since 1.2.0
     * @param boolean $insert Place insert buttons instead of edit links.
     */
    public function ajax_contents($insert = false)
    {
        global $wpdb;

        // Load input vars.
        $admin = H5P_Plugin_Admin::get_instance();
        list($offset, $limit, $sort_by, $sort_dir, $filters, $facets) = $admin->get_data_view_input();

        // Different fields for insert
        if ($insert) {
            $fields = array('title', 'content_type', 'tags', 'updated_at', 'id', 'content_type_id', 'slug');
        } else {
            $fields = array(
                'title',
                'content_type',
                'user_name',
                'tags',
                'updated_at',
                'id',
                'user_id',
                'content_type_id'
            );
        }

        // Add filters to data query
        $conditions = array();
        if (isset($filters[0])) {
            $conditions[] = array('title', $filters[0], 'LIKE');
        }

        if ($facets !== null) {
            $facetmap = array(
                'content_type' => 'content_type_id',
                'user_name' => 'user_id',
                'tags' => 'tags'
            );
            foreach ($facets as $field => $value) {
                if (isset($facetmap[$fields[$field]])) {
                    $conditions[] = array($facetmap[$fields[$field]], $value, '=');
                }
            }
        }

        // Create new content query
        $content_query = new H5PContentQuery($fields, $offset, $limit, $fields[$sort_by], $sort_dir, $conditions);
        $results = $content_query->get_rows();

        // Make data more readable for humans
        $rows = array();
        foreach ($results as $result) {
            $rows[] = ($insert ? $this->get_contents_insert_row($result) : $this->get_contents_row($result));
        }

        // Print results
        header('Cache-Control: no-cache');
        header('Content-type: application/json');
        print json_encode(array(
            'num' => $content_query->get_total(),
            'rows' => $rows
        ));
        exit;
    }

    /**
     * Format time for use in content lists.
     *
     * @since 1.6.0
     * @param int $timestamp
     * @return string
     */
    private function format_time($timestamp)
    {
        // Get timezone offset
        $offset = get_option('gmt_offset') * 3600;

        // Format time
        $time = strtotime($timestamp);
        $current_time = current_time('timestamp');
        $human_time = human_time_diff($time + $offset, $current_time) . ' ' . __('ago', $this->plugin_slug);

        if ($current_time > $time + DAY_IN_SECONDS) {
            // Over a day old, swap human time for formatted time
            $formatted_time = $human_time;
            $human_time = date('Y/m/d', $time + $offset);
        } else {
            $formatted_time = date(get_option('time_format'), $time + $offset);
        }

        $iso_time = date('c', $time);

        return "<time datetime=\"{$iso_time}\" title=\"{$formatted_time}\">{$human_time}</time>";
    }

    /**
     * Format tags for use in content lists.
     *
     * @since 1.6.0
     * @param string $tags
     * @return array With tag objects
     */
    private function format_tags($tags)
    {
        // Tags come in CSV format, create Array instead
        $result = array();
        $csvtags = explode(';', $tags);
        foreach ($csvtags as $csvtag) {
            if ($csvtag !== '') {
                $tag = explode(',', $csvtag);
                $result[] = array(
                    'id' => $tag[0],
                    'title' => esc_html($tag[1])
                );
            }
        }

        return $result;
    }

    /**
     * Get row for insert table with all values escaped and ready for view.
     *
     * @since 1.2.0
     * @param stdClass $result Database result for row
     * @return array
     */
    private function get_contents_insert_row($result)
    {
        return array(
            esc_html($result->title),
            array(
                'id' => $result->content_type_id,
                'title' => esc_html($result->content_type)
            ),
            $this->format_tags($result->tags),
            $this->format_time($result->updated_at),
            '<button class="button h5p-insert" data-id="' . $result->id . '" data-slug="' . $result->slug . '">' . __('Insert',
                $this->plugin_slug) . '</button>'
        );
    }

    /**
     * Get row for contents table with all values escaped and ready for view.
     *
     * @since 1.2.0
     * @param stdClass $result Database result for row
     * @return array
     */
    private function get_contents_row($result)
    {
        $row = array(
            '<a href="' . admin_url('admin.php?page=h5p&task=show&id=' . $result->id) . '">' . esc_html($result->title) . '</a>',
            array(
                'id' => $result->content_type_id,
                'title' => esc_html($result->content_type)
            ),
            array(
                'id' => $result->user_id,
                'title' => esc_html($result->user_name)
            ),
            $this->format_tags($result->tags),
            $this->format_time($result->updated_at),
            $result->id
        );

        $content = array('user_id' => $result->user_id);

        // Add user results link
        if (get_option('h5p_track_user', true)) {
            if ($this->current_user_can_view_content_results($content)) {
                $row[] = '<a href="' . admin_url('admin.php?page=h5p&task=results&id=' . $result->id) . '">' . __('Results',
                        $this->plugin_slug) . '</a>';
            } else {
                $row[] = '';
            }
        }

        // Add edit link
        if ($this->current_user_can_edit($content)) {
            $row[] = '<a href="' . admin_url('admin.php?page=h5p_new&id=' . $result->id) . '">' . __('Edit',
                    $this->plugin_slug) . '</a>';
        } else {
            $row[] = '';
        }

        return $row;
    }

    /**
     * Returns the instance of the h5p editor library.
     *
     * @since 1.1.0
     * @return \H5peditor
     */
    private function get_h5peditor_instance()
    {
        if (self::$h5peditor === null) {
            $upload_dir = wp_upload_dir();
            $plugin = H5P_Plugin::get_instance();
            self::$h5peditor = new H5peditor(
                $plugin->get_h5p_instance('core'),
                new H5PEditorWordPressStorage(),
                '',
                $plugin->get_h5p_path()
            );
        }

        return self::$h5peditor;
    }

    /**
     * Add assets and JavaScript settings for the editor.
     *
     * @since 1.1.0
     * @param int $id optional content identifier
     */
    public function add_editor_assets($id = null)
    {
        $plugin = H5P_Plugin::get_instance();
        $plugin->add_core_assets();

        // Make sure the h5p classes are loaded
        $plugin->get_h5p_instance('core');
        $this->get_h5peditor_instance();

        // Add JavaScript settings
        $settings = $plugin->get_settings();
        $cache_buster = '?ver=' . H5P_Plugin::VERSION;

        // Use jQuery and styles from core.
        $assets = array(
            'css' => $settings['core']['styles'],
            'js' => $settings['core']['scripts']
        );

        // Add editor styles
        foreach (H5peditor::$styles as $style) {
            $assets['css'][] = self::PATH_STYLES . str_replace('styles/', '', $style) . $cache_buster;
        }

        // Add editor JavaScript
        foreach (H5peditor::$scripts as $script) {
            // We do not want the creator of the iframe inside the iframe
            if ($script !== 'scripts/h5peditor-editor.js') {
                $assets['js'][] = self::PATH_SCRIPTS . $script . $cache_buster;
            }
        }

        // Add JavaScript with library framework integration (editor part)
        H5P_Plugin_Admin::add_script('editor-editor', self::PATH_SCRIPTS . 'scripts/h5peditor-editor.js');
        H5P_Plugin_Admin::add_script('editor', self::PATH_SCRIPTS . 'scripts/h5p-editor.js');

        // Add translation
        //$language = $plugin->get_language();
        $language_script = self::PATH_SCRIPTS . 'language/en.js';
        //if (!file_exists(plugin_dir_path(__FILE__) . '../' . $language_script)) {
        //    $language_script = 'h5p-editor-php-library/language/en.js';
        //}
        H5P_Plugin_Admin::add_script('language', $language_script);

        // Add JavaScript settings
        $content_validator = $plugin->get_h5p_instance('contentvalidator');
        $settings['editor'] = array(
            'filesPath' => $plugin->get_h5p_url() . '/editor',
            'fileIcon' => array(
                'path' => '/images/h5p/binary-file.png',
                'width' => 50,
                'height' => 50,
            ),
            'ajaxPath' => admin_url('/h5p/ajax_'),
            'libraryUrl' => 'h5p/h5p-editor-php-library/h5peditor.class.php',
            'copyrightSemantics' => $content_validator->getCopyrightSemantics(),
            'assets' => $assets,
            'deleteMessage' => __('Are you sure you wish to delete this content?'),
            'uploadToken' => ''
        );

        if ($id !== null) {
            $settings['editor']['nodeVersionId'] = $id;
        }

        $plugin->print_settings($settings);
    }

    public function get_editor_assets($id = null)
    {
        ob_start();
        $this->add_editor_assets($id);

        return ob_get_clean();
    }

    /**
     * Get library details through AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_libraries()
    {
        $editor = $this->get_h5peditor_instance();

        $name = filter_input(INPUT_GET, 'machineName', FILTER_SANITIZE_STRING);
        $major_version = filter_input(INPUT_GET, 'majorVersion', FILTER_SANITIZE_NUMBER_INT);
        $minor_version = filter_input(INPUT_GET, 'minorVersion', FILTER_SANITIZE_NUMBER_INT);

        header('Cache-Control: no-cache');
        header('Content-type: application/json');

        if ($name) {
            $plugin = H5P_Plugin::get_instance();
            print $editor->getLibraryData($name, $major_version, $minor_version, $plugin->get_language(),
                $plugin->get_h5p_path());

            // Log library load
            /*new H5P_Event('library', null,
                null, null,
                $name, $major_version . '.' . $minor_version);*/
        } else {
            print $editor->getLibraries();
        }

        exit;
    }

    /**
     * Handle file uploads through AJAX.
     *
     * @since 1.1.0
     */
    public function ajax_files()
    {
        $plugin = H5P_Plugin::get_instance();
        $files_directory = $plugin->get_h5p_path();

        if (!wp_verify_nonce(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING), 'h5p_editor_upload')) {
            H5PCore::ajaxError(__('Invalid security token. Please reload the editor.', $this->plugin_slug));
            exit;
        }

        $contentId = filter_input(INPUT_POST, 'contentId', FILTER_SANITIZE_NUMBER_INT);
        if ($contentId) {
            $files_directory .= '/content/' . $contentId;
        } else {
            $files_directory .= '/editor';
        }

        $editor = $this->get_h5peditor_instance();
        $interface = $plugin->get_h5p_instance('interface');
        $file = new H5peditorFile($interface, $files_directory);

        if (!$file->isLoaded()) {
            H5PCore::ajaxError(__('File not found on server. Check file upload settings.', $this->plugin_slug));
            exit;
        }

        if ($file->validate() && $file->copy()) {
            // Keep track of temporary files so they can be cleaned up later.
            $editor->addTmpFile($file);
        }

        header('Cache-Control: no-cache');
        header('Content-type: application/json; charset=utf-8');

        print $file->getResult();
        exit;
    }

    /**
     * Provide data for content results view.
     *
     * @since 1.2.0
     */
    public function ajax_content_results()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            return; // Missing id
        }

        $plugin = H5P_Plugin::get_instance();
        $content = $plugin->get_content($id);
        if (is_string($content) || !$this->current_user_can_edit($content)) {
            return; // Error loading content or no access
        }

        $plugin_admin = H5P_Plugin_Admin::get_instance();
        $plugin_admin->print_results($id);
    }
}
