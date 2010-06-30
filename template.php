<?php
/**
* Override or insert PHPTemplate variables into the search_theme_form template.
*
* @param $vars
*   A sequential array of variables to pass to the theme template.
* @param $hook
*   The name of the theme function being called (not used in this case.)
*/
function terrafirma2_preprocess_search_theme_form(&$vars, $hook) {
  // Note that in order to theme a search block you should rename this function
  // to terrafirma2_preprocess_search_block_form and use
  // 'search_block_form' instead of 'search_theme_form' in the customizations
  // bellow.

  // Modify elements of the search form
  $vars['form']['search_theme_form']['#title'] = t('');
 
  // Set a default value for the search box
  $vars['form']['search_theme_form']['#value'] = t('Search this Site');
 
  // Add a custom class and placeholder text to the search box
  $vars['form']['search_theme_form']['#attributes'] = array('class' => 'input-text',
                                                              'onfocus' => "if (this.value == 'Search this Site') {this.value = '';}",
                                                              'onblur' => "if (this.value == '') {this.value = 'Search this Site';}");
 
  // Change the text on the submit button
  //$vars['form']['submit']['#value'] = t('Go');

  // Rebuild the rendered version (search form only, rest remains unchanged)
  unset($vars['form']['search_theme_form']['#printed']);
  $vars['search']['search_theme_form'] = drupal_render($vars['form']['search_theme_form']);

  $vars['form']['submit']['#attributes'] = array('class' => 'input-submit');

  //$vars['form']['submit']['#type'] = 'image_button';
  //$vars['form']['submit']['#src'] = path_to_theme() . '/images/search.png';
   
  // Rebuild the rendered version (submit button, rest remains unchanged)
  unset($vars['form']['submit']['#printed']);
  $vars['search']['submit'] = drupal_render($vars['form']['submit']);

  // Collect all form elements to make it easier to print the whole form.
  $vars['search_form'] = implode($vars['search']);
}



function terrafirma2_links($links, $attributes = array('class' => 'links')) {
  global $language;
  $output = '';

  if (count($links) > 0) {

 		$output = '<ul'. drupal_attributes($attributes) .'>';
 			
    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = $key;

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class .= ' first';
      }
      if ($i == $num_links) {
        $class .= ' last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language->language)) {
        $class .= ' active';
      }

      $output .= '<li'. drupal_attributes(array('class' => $class)) .'>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      else if (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span'. $span_attributes .'>'. $link['title'] .'</span>';
      }

      $i++;

      $output .= "</li>\n";
    }

  $output .= "</ul>";
}

  return $output;
}





function terrafirma2_preprocess_node(&$variables) {
  $node = $variables['node'];
  if (module_exists('taxonomy')) {
    $variables['taxonomy'] = taxonomy_link('taxonomy terms', $node);
  }
  else {
    $variables['taxonomy'] = array();
  }

  if ($variables['teaser'] && $node->teaser) {
    $variables['content'] = $node->teaser;
  }
  elseif (isset($node->body)) {
    $variables['content'] = $node->body;
  }
  else {
    $variables['content'] = '';
  }

  $variables['date']      = format_date($node->created, 'custom', 'F j, Y');
  $variables['links']     = !empty($node->links) ? theme('links', $node->links, array('class' => 'links inline')) : '';
  $variables['name']      = theme('username', $node);
  $variables['node_url']  = url('node/'. $node->nid);
  $variables['terms']     = theme('links', $variables['taxonomy'], array('class' => 'terms')); 
  $variables['title']     = check_plain($node->title);

  // Flatten the node object's member fields.
  $variables = array_merge((array)$node, $variables);

  // Display info only on certain node types.
  if (theme_get_setting('toggle_node_info_'. $node->type)) {
    $variables['submitted'] = theme('node_submitted', $node);
    $variables['picture'] = theme_get_setting('toggle_node_user_picture') ? theme('user_picture', $node) : '';
  }
  else {
    $variables['submitted'] = '';
    $variables['picture'] = '';
  }
  // Clean up name so there are no underscores.
  $variables['template_files'][] = 'node-'. $node->type;
}

function terrafirma2_preprocess_page(&$variables) {
  // Add favicon
  if (theme_get_setting('toggle_favicon')) {
    drupal_set_html_head('<link rel="shortcut icon" href="'. check_url(theme_get_setting('favicon')) .'" type="image/x-icon" />');
  }

  global $theme;
  // Populate all block regions.
  $regions = system_region_list($theme);
  // Load all region content assigned via blocks.
  foreach (array_keys($regions) as $region) {
    // Prevent left and right regions from rendering blocks when 'show_blocks' == FALSE.
    if (!(!$variables['show_blocks'] && ($region == 'left' || $region == 'right'))) {
      $blocks = theme('blocks', $region);
    }
    else {
      $blocks = '';
    }
    // Assign region to a region variable.
    isset($variables[$region]) ? $variables[$region] .= $blocks : $variables[$region] = $blocks;
  }

  // Set up layout variable.
  $variables['layout'] = 'none';
  if (!empty($variables['left'])) {
    $variables['layout'] = 'left';
  }
  if (!empty($variables['right'])) {
    $variables['layout'] = ($variables['layout'] == 'left') ? 'both' : 'right';
  }

  // Set mission when viewing the frontpage.
  if (drupal_is_front_page()) {
    $mission = filter_xss_admin(theme_get_setting('mission'));
  }

  // Construct page title
  if (drupal_get_title()) {
    $head_title = array(strip_tags(drupal_get_title()), variable_get('site_name', 'Drupal'));
  }
  else {
    $head_title = array(variable_get('site_name', 'Drupal'));
    if (variable_get('site_slogan', '')) {
      $head_title[] = variable_get('site_slogan', '');
    }
  }
  $variables['head_title']        = implode(' | ', $head_title);
  $variables['base_path']         = base_path();
  $variables['front_page']        = url();
  $variables['breadcrumb']        = theme('breadcrumb', drupal_get_breadcrumb());
  $variables['feed_icons']        = drupal_get_feeds();
  $variables['footer_message']    = filter_xss_admin(variable_get('site_footer', FALSE));
  $variables['head']              = drupal_get_html_head();
  $variables['help']              = theme('help');
  $variables['language']          = $GLOBALS['language'];
  $variables['language']->dir     = $GLOBALS['language']->direction ? 'rtl' : 'ltr';
  $variables['logo']              = theme_get_setting('logo');
  $variables['messages']          = $variables['show_messages'] ? theme('status_messages') : '';
  $variables['mission']           = isset($mission) ? $mission : '';
  $variables['primary_links']     = theme_get_setting('toggle_primary_links') ? menu_primary_links() : array();
  $variables['secondary_links']   = theme_get_setting('toggle_secondary_links') ? menu_secondary_links() : array();
  $variables['search_box']        = (theme_get_setting('toggle_search') ? drupal_get_form('search_theme_form') : '');
  $variables['site_name']         = (theme_get_setting('toggle_name') ? filter_xss_admin(variable_get('site_name', 'Drupal')) : '');
  $variables['site_slogan']       = (theme_get_setting('toggle_slogan') ? filter_xss_admin(variable_get('site_slogan', '')) : '');
  $variables['css']               = drupal_add_css();
  $variables['styles']            = drupal_get_css();
  $variables['scripts']           = drupal_get_js();
  $variables['tabs']              = theme('menu_local_tasks');
  $variables['title']             = drupal_get_title();
  // Closure should be filled last.
  $variables['closure']           = theme('closure');

  if ($node = menu_get_object()) {
    $variables['node'] = $node;
  }

  // Compile a list of classes that are going to be applied to the body element.
  // This allows advanced theming based on context (home page, node of certain type, etc.).
  $body_classes = array();
  // Add a class that tells us whether we're on the front page or not.
  $body_classes[] = $variables['is_front'] ? 'front' : 'not-front';
  // Add a class that tells us whether the page is viewed by an authenticated user or not.
  $body_classes[] = $variables['logged_in'] ? 'logged-in' : 'not-logged-in';
  // Add arg(0) to make it possible to theme the page depending on the current page
  // type (e.g. node, admin, user, etc.). To avoid illegal characters in the class,
  // we're removing everything disallowed. We are not using 'a-z' as that might leave
  // in certain international characters (e.g. German umlauts).
  $body_classes[] = preg_replace('![^abcdefghijklmnopqrstuvwxyz0-9-_]+!s', '', 'page-'. form_clean_id(drupal_strtolower(arg(0))));
  // If on an individual node page, add the node type.
  if (isset($variables['node']) && $variables['node']->type) {
    $body_classes[] = 'node-type-'. form_clean_id($variables['node']->type);
  }
  // Add information about the number of sidebars.
  if ($variables['layout'] == 'both') {
    $body_classes[] = 'two-sidebars';
  }
  elseif ($variables['layout'] == 'none') {
    $body_classes[] = 'no-sidebars';
  }
  else {
    $body_classes[] = 'one-sidebar sidebar-'. $variables['layout'];
  }
  // Implode with spaces.
  $variables['body_classes'] = implode(' ', $body_classes);

  // Build a list of suggested template files in order of specificity. One
  // suggestion is made for every element of the current path, though
  // numeric elements are not carried to subsequent suggestions. For example,
  // http://www.example.com/node/1/edit would result in the following
  // suggestions:
  //
  // page-node-edit.tpl.php
  // page-node-1.tpl.php
  // page-node.tpl.php
  // page.tpl.php
  $i = 0;
  $suggestion = 'page';
  $suggestions = array();
  while ($arg = arg($i++)) {
    $arg = str_replace(array("/", "\\", "\0"), '', $arg);
    $suggestions[] = $suggestion .'-'. $arg;
    if (!is_numeric($arg)) {
      $suggestion .= '-'. $arg;
    }
  }
  if (drupal_is_front_page()) {
    $suggestions[] = 'page-front';
  }

  if ($suggestions) {
    $variables['template_files'] = $suggestions;
  }
}


