<form id="le-membership-dialog">
	<h1><?php _e('Membership Settings',OP_SN) ?></h1>
	<div class="op-lightbox-content">
	   <div class="settings-container">
	<div class="op-bsw-grey-panel-fixed">
		<div class="op-bsw-grey-panel-content cf op-bsw-grey-panel-no-sidebar module-live_editor">
			<div class="op-bsw-grey-panel-tab-content-container cf">
				<div class="op-bsw-grey-panel-tab-content tab-layout op-bsw-grey-panel-tab-content-selected">
					<?php
					if (defined("WS_PLUGIN__OPTIMIZEMEMBER_VERSION")) : // OptimizeMember plugin is activated ?>
					<!-- START -->
					<div class="op-bsw-grey-panel section-feature_title">
						<div class="op-bsw-grey-panel-header cf">
							<h3>
								<a href="#">Membership Page Restrictions</a>
							</h3>
							<div class="op-bsw-panel-controls cf">
								<div class="show-hide-panel">
									<a href="#"></a>
								</div>

								<div class="panel-control">
									
								</div>
							</div>
						</div>
						<div id="op_page_layout_footer_area" class="op-bsw-grey-panel-content op-bsw-grey-panel-no-sidebar cf">
							<p><?php _e('Customize required membership levels and packages needed to access this page.',OP_SN) ?></p>
							<?php
								global $post;
								echo '<input type="hidden" name="post_type" value="'.$post->post_type.'" />';
								c_ws_plugin__optimizemember_meta_box_security::optimizeMemberOptions($post); 
								
							?>
						</div>
					</div>
					<!-- ****** -->
					<?php endif; 
					global $post;
					$memType = get_post_meta($post->ID, 'type', true);
					$type = op_page_option('theme','type');
					$memOptions = op_page_option('membership');
					$parent = array_reverse(get_post_ancestors($post->ID));
					//die(print_r($parent));
					if ($type == 'membership') :
					$presets = array(
						'blank' => __('Blank Page',OP_SN),
						'sidebar' => __('Page with sidebar',OP_SN),
						'module_listing' => __('Module listings',OP_SN),
					);
					$this->js['membership_types'] = $js_options;
					$blankimg = 'pb_page_blank.png';
					//$selected = 'blank';
					$preset_options = array();
					$default = array(
						'width' => 206,
						'height' => 147,
					);
					$selected = $memOptions['layout'];
					foreach($presets as $name => $title){
						$li_class = $input_attr = '';
						if($selected == $name){
							$input_attr = ' checked="checked"';
							$li_class = 'img-radio-selected';
						}
						$preset_options[] = array_merge($default,array(
							'input' => '<input type="radio" name="op[page][preset_option]" value="'.$name.'"'.$input_attr.' />',
							'image' => OP_IMG.'page_types/'.$blankimg,//($name=='blank'?$blankimg:'pb_page_'.$name.'.png'),
							'preview_content' => $title,
							'li_class' => $li_class,
						));	
					}
					?>
					<!-- START -->
					<div class="op-bsw-grey-panel section-feature_title">
						<div class="op-bsw-grey-panel-header cf">
							<h3>
								<a href="#">Membership Page Settings</a>
							</h3>
							<div class="op-bsw-panel-controls cf">
								<div class="show-hide-panel">
									<a href="#"></a>
								</div>

								<div class="panel-control">
									
								</div>
							</div>
						</div>
						<div id="op_page_layout_footer_area" class="op-bsw-grey-panel-content op-bsw-grey-panel-no-sidebar cf">
							<?php if ($memType == 'product') : ?>
							<div class="page-options" id="productName">
								<label class="form-title bold-title" for="op_product_name"><?php _e('Product Name',OP_SN) ?></label>
								<input type="text" name="op[product][name]"
									value="<?php echo $post->post_title; ?>" />
							</div>
							<?php /*
							<label><?php _e('Select product content type', OP_SN) ?></label>
								<p><?php _e('This is how it will be presented', OP_SN) ?></p>
								<div id="preset-option">
								<?php echo $this->load_tpl('generic/img_radio_selector',array('previews'=>$preset_options,'classextra'=>'preset-type-select')); ?> 
								</div> */ ?>
							<?php endif; ?>
							<input type="hidden" name="op[theme]" id="opThemeDir"
								value="<?php echo op_page_option('theme', 'dir');?>" /> <input
								type="hidden" name="op[pageId]" value="<?php echo $post->ID; ?>" />
							<?php if ($memType == 'category' || $memType == 'subcategory' || $memType == 'content') :?>
							<div id="pageType">
								<label><?php _e('Select product', OP_SN) ?></label>
								<p><?php _e('Select product for the content you are creating to be assigned to',OP_SN) ?></p>
								<select name="op[pageType][product]" id="op_product_id">
									<?php echo select_html('product', $parent[0]); ?>
								</select> <label><?php _e('Select a page type', OP_SN);?></label>
								<p></p>
								<select id="pageTypeChange" name="op[pageType][type]"
									disabled="disabled">
									<option value="">---</option>
									<option value="category"
										<?php echo $memType == 'category' ? 'selected="selected"': ''?>><?php _e('Category/Module', OP_SN);?></option>
									<option value="subcategory"
										<?php echo $memType == 'subcategory' ? 'selected="selected"': ''?>><?php _e('Subcategory/Submodule', OP_SN);?></option>
									<option value="content"
										<?php echo $memType == 'content' ? 'selected="selected"': ''?>><?php _e('Content/Post/Lesson', OP_SN);?></option>
								</select>
							</div>
							<?php endif; ?>
							<?php if ($memType == 'category') :?>
							<div id="category">
								<label><?php _e('Category naming', OP_SN) ?></label>
								<p><?php _e('Enter the title for the category', OP_SN) ?></p>
								<input id="opCategoryName" type="text" name="op[category][name]"
									value="<?php echo $post->post_title; ?>" /> <label><?php _e('Category description', OP_SN) ?></label>
								<p><?php _e('Enter the description for the category', OP_SN) ?></p>
								<textarea name="op[category][description]"><?php echo stripslashes(base64_decode(op_page_option('membership', 'description')));?></textarea>
								
								<?php /*
							<label><?php _e('Select product content type', OP_SN) ?></label>
								<p><?php _e('This is how it will be presented', OP_SN) ?></p>
								<div id="preset-option">
								<?php echo $this->load_tpl('generic/img_radio_selector',array('previews'=>$preset_options,'classextra'=>'preset-type-select')); ?> 
								</div> */ ?>
							</div>
							<?php endif; ?>
							<?php if ($memType == 'subcategory') :?>
							<div id="subcategory">
								<label><?php _e('Select category', OP_SN) ?></label>
								<p><?php _e('Select category to nest this subcategory under',OP_SN) ?></p>
								<select name="op[subcategory][category]" id="op_category_id">
									<?php echo select_html('category', $parent[1]); ?>
								</select> <label><?php _e('Subcategory naming', OP_SN) ?></label>
								<p><?php _e('Enter the title for the subcategory', OP_SN) ?></p>
								<input id="opSubCategoryName" type="text"
									name="op[subcategory][name]"
									value="<?php echo $post->post_title; ?>" /> <label><?php _e('Subcategory description', OP_SN) ?></label>
								<p><?php _e('Enter the description for the subcategory', OP_SN) ?></p>
								<textarea name="op[subcategory][description]"><?php echo stripslashes(base64_decode(op_page_option('membership', 'description')));?></textarea>
								<?php /*
							<label><?php _e('Select product content type', OP_SN) ?></label>
								<p><?php _e('This is how it will be presented', OP_SN) ?></p>
								<div id="preset-option">
								<?php echo $this->load_tpl('generic/img_radio_selector',array('previews'=>$preset_options,'classextra'=>'preset-type-select')); ?> 
								</div> */ ?>
							</div>
							<?php endif;?>
							<?php if ($memType == 'content') :?>
							<div id="content">
								<div style="float: left; width: 50%;">
									<label><?php _e('Select category', OP_SN) ?></label>
									<p><?php _e('Select category to nest this content under',OP_SN) ?></p>
									<select name="op[content][category]" id="op_category_id">
									<?php echo select_html('category', $parent[1]); ?>
								</select>
								</div>
								<div style="float: right; width: 50%;">
									<label><?php _e('Select subcategory', OP_SN) ?></label>
									<p><?php _e('Select subcategory to nest this content under',OP_SN) ?></p>
									<select name="op[content][subcategory]" id="op_subcategory_id">
									<?php echo select_html('subcategory', $parent[2]); ?>
								</select>
								</div>
								<div style="clear: both;"></div>
								<label><?php _e('Content naming', OP_SN) ?></label>
								<p><?php _e('Enter the title for the content', OP_SN) ?></p>
								<input id="opContentName" type="text" name="op[content][name]"
									value="<?php echo $post->post_title; ?>" /> <label><?php _e('Content description', OP_SN) ?></label>
								<p><?php _e('Enter the description for the content', OP_SN) ?></p>
								<textarea name="op[content][description]"><?php echo stripslashes(base64_decode(op_page_option('membership', 'description')));?></textarea>
								<?php /*
							<label><?php _e('Select product content type', OP_SN) ?></label>
								<p><?php _e('This is how it will be presented', OP_SN) ?></p>
								<div id="preset-option">
								<?php echo $this->load_tpl('generic/img_radio_selector',array('previews'=>$preset_options,'classextra'=>'preset-type-select')); ?> 
								</div> */ ?>
							</div> 
							<?php endif;?>
							<?php if (empty($memType)) :?>
							<div id="content">
								<div style="clear: both;">
									<label><?php _e('Select membership type', OP_SN) ?></label>
									<p><?php _e('After this your page will refresh and you will have other options based on this selection in this screen.', OP_SN) ?></p>
									<select name="op[type]" id="op_type_id">
										<option value="product"><?php _e('Product', OP_SN);?></option>
										<option value="category"><?php _e('Category', OP_SN);?></option>
										<option value="subcategory"><?php _e('Subcategory', OP_SN);?></option>
										<option value="content"><?php _e('Content/module', OP_SN);?></option>
									</select>
								</div>
							</div>
							<?php endif; ?>
						</div>
						
					</div>
					<?php endif; ?>
					<!-- ****** -->
				</div>
			</div>
			
		</div>
	</div>
	<div class="clear"></div>
</div>

	</div>
	<div class="op-insert-button cf">
		<button type="submit" class="editor-button">
			<span><?php _e('Update',OP_SN) ?></span>
		</button>
	</div>
</form>
<?php 
function select_html($type, $selected_id=0, $parent_id=0) {
		global $wpdb;
		$select_html = '<option value="" class="default-val"></option>';
		$query = "SELECT o.id, o.post_parent, o.post_title FROM {$wpdb->prefix}posts o INNER JOIN {$wpdb->postmeta} p ON o.id = p.post_id WHERE p.meta_key = 'type' AND p.meta_value = '{$type}' ORDER BY o.post_title ASC";
		if($rows = $wpdb->get_results($query)){
			foreach($rows as $row){
				$select_html .= '<option value="'.$row->id.'"'.($selected_id == $row->id?' selected="selected"':'').' class="parent-'.$row->post_parent.($row->post_parent != $parent_id?'':'').'">'.$row->post_title.'</option>';
			}
		}
		return $select_html;
	}
	?>