<?php
/*
#################################################################################
#  Module ANY FEED PRO for Opencart 2.x From HostJars opencart.hostjars.com #
#################################################################################
*/
?>
<style type="text/css">
    .name-label {
        font-size: 22px;
        font-weight: 600;
        color: #72aa00;
    }
    
    .accordiona {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 10px;
        width: 100%;
        //border: none;
        text-align: left;
        outline: none;
        font-size: 12px;
        transition: 0.4s;
        border-bottom-style: 1px solid #ccc;
    }

    .activea, .accordiona:hover {
        background-color: #ccc; 
    }

    .panela {
        padding: 0 18px;
        display: none;
        background-color: white;
        overflow: hidden;
    }
    
    .accordiona:after {
        content: '\02795'; /* Unicode character for "plus" sign (+) */
        font-size: 13px;
        color: #777;
        float: right;
        margin-left: 5px;
    }

    .activea:after {
        content: "\2796"; /* Unicode character for "minus" sign (-) */
    }
</style>

<?php echo $header; ?><?php echo $menu; ?>

<div id="content">

<?php if (isset($jquery)) echo $jquery; ?>
<?php if (isset($css)) echo $css; ?>

<div class="page-header">
<div class="container-fluid">
	<h1><?php echo $heading_title; ?> <a href="http://helpdesk.hostjars.com/entries/22366881-any-feed" target="_blank"><i class="fa fa-info-circle"></i></a></h1>
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="buttons pull-right">
	    <button class="button btn btn-primary" onclick="window.location.href = 'index.php?route=feed/any_feed_pro/createFeed&token=<?php echo $token ?>'"><?php echo $entry_add_feed; ?></button>
            <button class="button btn btn-primary" onclick="window.location.href = 'index.php?route=feed/any_feed_pro/manageProfile&token=<?php echo $token ?>'" style="background-color: coral; border-color: coral;"><?php echo "Manage Profiles"; ?></button>
	    <button onclick="return saveFeedsAjax();" class="button btn btn-success"><?php echo $button_save; ?></button>
	    <button onclick="location = '<?php echo $cancel; ?>';" class="button btn btn-danger"><?php echo $button_cancel; ?></button>
	</div>
</div>
</div>
  <div class="container-fluid"window>
<?php if ($error_warning) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<?php if ($success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
<?php } ?>

<div id="ajaxFade"></div>
<div id="ajaxSpinner"><p>Loading..</p><i class="fa fa-spinner fa-spin fa-3x"></i></div>

<div class="panel panel-default">
   <div class="panel-body">

	<!-- fields to include -->
		<div id="field_content_source" class="draggable">
		  	<h2><?php echo $entry_exclude_fields; ?></h2>
		   	<div id="field_source">
		    	<div class="inital_portlet_placement"></div>
		    	<?php foreach($source_fields as $source_name => $src_fields) {?>
		    		<div class="portlet feed_source">
		    			<div class="portlet-header"><span class="field_name"><?php echo $source_name; ?></span></div>
		    			<div class="portlet-content">
						<!-- Field settings common to all fields -->
						<?php foreach($common_field_settings as $common_setting_text => $common_field_setting) {?>
							<?php if($common_setting_text != 'prefix') {?>
								<label for="<?php echo $common_field_setting['name']; ?>"><?php echo $common_setting_text; ?>: </label>

								<?php if($common_field_setting['type'] == 'text') {?>
									<br /><input type="text" class="field_text" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $common_setting_text;?>][value]" value="<?php echo $source_name; ?>"/>
								<?php } ?>

								<?php if($common_field_setting['type'] == 'checkbox') {?>
									<input type="checkbox" class="field_checkbox" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $common_setting_text;?>][value]"/>
								<?php } ?>

								<?php if($common_field_setting['type'] == 'rule') { ?>
									<button class='btn btn-link' onclick='javascript:addRule("<?php echo $source_name; ?>", $(this).siblings(".rule").length, this);updateFieldNames();return false;'><i class="fa fa-plus"></i></button>
									<br class="rules_below" />

								<?php } ?>

								<input type='hidden' name='feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $common_setting_text;?>][type]' class='field_text num_reference' value='<?php echo $common_field_setting['type'] ?>'/>
								<input type='hidden' name='feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $common_setting_text;?>][name]' class='field_text' value='<?php echo $common_field_setting['name']; ?>'/>
								<br />
							<?php } ?>
						<?php } ?>
						<!-- Field settings specific to the one field -->
						<?php foreach($src_fields as $setting_text => $field_settings) {?>
							<?php if($setting_text != 'prefix') {?>
								<label for="<?php echo $field_settings['name']; ?>"><?php echo $setting_text; ?>: </label>

								<?php if($field_settings['type'] == 'text') {?>
									<br /><input type="text" class="field_text" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][value]"/>
								<?php } ?>

								<?php if($field_settings['type'] == 'checkbox') {?>
									<?php ($field_settings['value'] == 1) ? $checked='checked="checked"':$checked='' ;?>
									<input type="checkbox" class="field_checkbox" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][value]" value="<?php echo $field_settings['value'];?>" <?php echo $checked;?>/>
								<?php } ?>

								<?php if($field_settings['type'] == 'unit') {?>
									<select name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][location]">
										<option value="after">after</option>
										<option value="before">before</option>
										<option value="none">none</option>
									</select>
									<input type="text" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][value]" placeholder="unit symbol"/>
								<?php } ?>

								<?php if($field_settings['type'] == 'select') {?>
									<select name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text; ?>][value]">
										<?php foreach ($select_options[$field_settings['name']] as $key => $option_name) { ?>
											<option value="<?php echo $key; ?>"><?php echo $option_name; ?></option>
										<?php } ?>
									</select>
								<?php } ?>

								<?php if ($field_settings['type'] == 'customer_group') { ?>
									<select name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text; ?>][value]">
										<?php foreach ($customer_groups as $customer_group) { ?>
                                        	<option value="<?php echo $customer_group['customer_group_id']; ?>">
                                        		<?php echo $customer_group['name']; ?>
                                        	</option>
                                		<?php } ?>
                            		</select>
								<?php } ?>

								<?php if($field_settings['type'] == 'name_map') { ?>
            					<div class='name_map_container'>
            						<select class="name_map_enabled" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text; ?>][enabled]">

            							<option value="0">Default</option>

            							<option <?php if (empty($name_map[$field_settings['name']])) { echo "disabled"; } ?> value="1">Custom</option>
        							</select>
	            					<div class='name_map_list'>
	            						<table>
	            						<tr>
		            						<td>Existing <?php echo $field_settings['name']; ?></td>
											<td>New Name</td>
										</tr>
            						<?php foreach ($name_map[$field_settings['name']] as $name_map_val) { ?>
	            						<tr>
		            						<td>
	            								<input class="origName" type="text" readonly="readonly" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text; ?>][names][<?php echo $name_map_val['id']; ?>][original]" value="<?php echo $name_map_val['name']; ?>">
            								</td>
            								<td>
												<input class="newName" type="text" name="feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text; ?>][names][<?php echo $name_map_val['id']; ?>][new]" placeholder="<?php echo $name_map_val['name']; ?>">
											</td>
										</tr>
									<?php } ?>
										</table>
	            					</div>
            					</div>
								<?php } ?>

								<input type='hidden' name='feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][type]' class='field_text num_reference' value='<?php echo $field_settings['type']?>'/>
								<input type='hidden' name='feed_name_replace[fields][<?php echo $source_name; ?>][settings][field_num_replace][<?php echo $setting_text;?>][name]' class='field_text' value='<?php echo $field_settings['name']; ?>'/>
								<br />
							<?php } ?>
						<?php } ?>
						</div>
		    		</div>
		    	<?php } ?>
			</div>
			<div class="clear"></div>
		</div>
                                        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" onclick="return false;">
			<div id="content_holder" class="field_content">
			</div>
			<div id="dialog-form" title="Create new feed">
			    <p class="validateTips"><?php echo $text_select_name; ?></p>
			    <fieldset>
			        <label for="name"><?php echo $text_feed_name; ?></label>
			        <input type="text" name="feed_name" id="feed_name" placeholder="Feed Name" />
			        <br />
			        <label for="feed_profile"><?php echo $text_feed_profile; ?></label>
			        <select name="profile" id="feed_profile"onchange="updateFeedName($(this).val());">
			        	<?php foreach($profiles as $name => $value) { ?>
			        		<option value="<?php echo $name ?>" class="profile_<?php echo $name ?>"><?php echo $value; ?></option>
			        	<?php } ?>
			        </select>
			    </fieldset>
			</div>
			<div id="dialog-confirm" title="Delete this feed?">
			    <p>Make sure to press "Save" once changes are completed. This feed will be permanently deleted. Are you sure?</p>
			</div>
			<div id="dialog-get-feed" title="Feed URL">
				<p>You can share this URL with customers and they can download your feed: </p>
				<input type='text' class='feed_url feed_url_dialog' onClick='this.setSelectionRange(0, this.value.length)' value='' readonly='readonly'>
			</div>
			<div id="dialog-rename" title="Rename this feed?">
				<input type="text" id="rename" placeholder="New Feed Name"></input>
			    <div>
			    	<p class="validateTips"><?php echo $text_select_name; ?></p>
			    </div>
			</div>
		</form>
		</div>
  </div>
</div>
</div>



<script type="text/javascript">
// Textarea and select clone() bug workaround | Spencer Tipping
// Licensed under the terms of the MIT source code license
(function (original) {
  jQuery.fn.clone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
    for (var i = 0, l = my_selects.length;   i < l; ++i) {
      for (var j = 0, m = my_selects[i].options.length; j < m; ++j) {
        if (my_selects[i].options[j].selected === true) {
          result_selects[i].options[j].selected = true;
        }
      }
    }
    return result;
  };
}) (jQuery.fn.clone);

function getFieldNumFromName(field_name) {
	var field_num = 0;
	if (typeof field_name != 'undefined') {
		var re = /settings\]\[([0-9])*\]\[Field Name/;
		var found = field_name.match(re);
		// Capture group 1 should contain field number
		if ((typeof found != 'undefined') && (found) && (found.hasOwnProperty(1)) )
			field_num = found[1];
	}
	return field_num;
}

function addRule(fieldName, ruleNum, context) {
	var field_name = $(context).siblings('.num_reference').attr('name');
	var fieldNum = getFieldNumFromName(field_name);
	var addElem = $(context).siblings('.rules_below');
	if ($(context).siblings('.rule').length) {
		addElem = $(context).siblings('.rule').last();
	}

	rule = '<span class="rule">If ' + fieldName + ' ';

	rule += '<select name="feed_name_replace[fields][' + fieldName + '][settings][' + fieldNum + '][Rules][rules][' + ruleNum + '][rule_comparator]">';
	<?php foreach ($rule_comparisons as $comparator) { ?>
		rule += '<option value="<?php echo $comparator['val']; ?>"><?php echo ($comparator['name']); ?></option>';
	<?php } ?>
	rule += '</select>';

	rule += '<input type="text" name="feed_name_replace[fields][' + fieldName + '][settings][' + fieldNum + '][Rules][rules][' + ruleNum + '][rule_comparator_value]"> then ';

	rule += '<select class="export_type" onchange="updateRuleExportInput(this)" name="feed_name_replace[fields][' + fieldName + '][settings][' + fieldNum + '][Rules][rules][' + ruleNum + '][rule_export_type]">';
	<?php foreach ($rule_export_types as $export_type) { ?>
		rule += "<option value='<?php echo $export_type['val']; ?>'><?php echo $export_type['name'] ?></option>";
	<?php } ?>
	rule += '</select>';

	rule += '<select class="math rule_export" style="display:none;">';
	<?php foreach ($math_operators as $k => $field) { ?>
		rule += '<option value="<?php echo $field; ?>"><?php echo $field; ?></option>';
	<?php } ?>
	rule += '</select>';

	rule += '<input class="rule_export" type="text">';

	rule += '<select class="product_fields rule_export" style="display:none;">';
	<?php foreach ($rule_export_fields as $k => $field) { ?>
		rule += '<option value="<?php echo $field; ?>"><?php echo $field; ?></option>';
	<?php } ?>
	rule += '</select>';

	rule += "<input type='hidden' class='rule_export_value' name='feed_name_replace[fields][" + fieldName + "][settings][" + fieldNum + "][Rules][rules][" + ruleNum + "][rule_export]'>";
	rule += "<input type='hidden' class='rule_math_operator' name='feed_name_replace[fields][" + fieldName + "][settings][" + fieldNum + "][Rules][rules][" + ruleNum + "][math_operator]'>";

	rule += "<button class='btn btn-link' onclick='javascript:$(this).closest(\".rule\").remove()'><i class='fa fa-trash-o'></i></button>";
	rule += "</span>";

	$(addElem).after(rule);
}

function updateRuleExportInput(ctx) {
	$(ctx).closest('.rule').children('.rule_export').hide();
	if ($(ctx).val() === 'txt' || $(ctx).val() === 'append') {
		$(ctx).closest('.rule').find(':text.rule_export').show();
	}
	else if ($(ctx).val() === 'slct') {
		$(ctx).closest('.rule').find('select.rule_export.product_fields').show();
	}
	else if ($(ctx).val() === 'math') {
		$(ctx).closest('.rule').find('.math.rule_export').show();
		$(ctx).closest('.rule').find(':text.rule_export').show();
	}
}

function updateFeedName(value) {
	$("#feed_name").val(prettyName(value));
}

function prettyName(name) {
	name = name.replace('/_/g', " ");
	name = name.charAt(0).toUpperCase() + name.slice(1);
	return name;
}

function encodedName(name) {
	name = name.replace(/\s/g, "_");
	name = validateName(name);
	name = name.toLowerCase();
	return name;
}

function validateName(name) {
	name = name.replace(/^[^a-zA-Z]*/g, '');
	name = name.replace(/[^a-zA-Z_\d]/g, '');
	return name;
}

function processRules() {
	$('.rule').each(function() {
		var ex_type = $(this).find('.export_type').val();
		if (ex_type === 'txt' || ex_type === 'append') {
			$(this).find('.rule_export_value').val($(this).find(':text.rule_export').val());
		}
		else if (ex_type === 'slct') {
			$(this).find('.rule_export_value').val($(this).find('select.rule_export.product_fields').val());
		}
		else if (ex_type === 'math') {
			$(this).find('.rule_export_value').val($(this).find(':text.rule_export').val());
			$(this).find('.rule_math_operator').val($(this).find('select.rule_export.math').val());
		}
	});
}

function saveFeeds() {
	processRules();
	$( '#form' ).submit();
}

function saveFeedsAjax() {
	processRules();
	$('#form .field_list').each(function (j, feed_list) {
		$(feed_list).find('.portlet').each(function (i, field) {
			var sort_input = $(field).find('input[name*="Field Name"]:eq(0)');
			var name = $(sort_input).attr('name').replace('[Field Name][value]', '[sort_order]');
			$(field).find('.portlet-content').append($('<input>').attr('name', name).attr('sort_order', '1').val(i));
		});
	});
	var data = $('#form').serialize();
	var url = 'index.php?route=feed/any_feed_pro/saveSettingsAjax&token=<?php echo $token ?>';
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		success: function(result) {
			if (result) {
				$('.breadcrumb').after('<div class="alert alert-success success">Feeds Saved Succesfully! Use the link in Feed Settings to view each feed.</div>');
				setTimeout(function(){ $('.success').fadeOut() }, 10000);                                
			}
		},
		beforeSend: function showSpinner() {
      $('#ajaxSpinner').show();
      $('#ajaxFade').show();
  	},
  	complete: function hideSpinner() {
			$('input[sort_order]').remove();
      $('#ajaxSpinner').hide();
      $('#ajaxFade').hide();
      // window.location.href = 'index.php?route=feed/any_feed_pro&token=<?php echo $token ?>';
    },
	});
}

function updateText(el) {
	if(typeof(el) == 'string') {
		action = $('.settings_feedtype.settings_'+el).val();
		parent = $('.settings_feedtype.settings_'+el).parents('.portlet');
	} else {
		var action = el.value;
		parent = $(el).parents('.portlet');
	}
	if(action == 'CSV') {
		$(parent).find('.XML').hide();
		$(parent).find('.CSV').show();
	} else {
		$(parent).find('.CSV').hide();
		$(parent).find('.XML').show();
	}
}

function updateFieldNames() {
	$('.field_content').each(function loopThroughAllFeeds()
	{
		var field_list = this;

		if ($(field_list).find('.profile_name').length)
			var profile = encodedName($(this).find('.profile_name').html());

		$(field_list).find('.portlet').each(function loopThroughAllPortlets() {
			var portlet_name = $(this).find('.field_name').html();

			var total_fields = $(field_list).find('input[name*="[fields]['+portlet_name+']"][name*="Field Name"][name*="value"]').length;
			var fields_unset = $(field_list).find('input[name*="[fields]['+portlet_name+']"][name*="Field Name"][name*="value"][name*="field_num_replace"]').length;
			var num_replace = total_fields - fields_unset;

			$(this).find(':input').each(function updateInputNames()
			{
				if ($(this).is("[name]"))
				{
					if ($(this).attr('name').match(/field_num_replace/g))
					{
						name = $(this).attr('name').replace('field_num_replace', num_replace);
						$(this).attr('name', name);
					}
					if ($(this).attr('name').match(/feed_name_replace/g))
					{
						name = $(this).attr('name').replace('feed_name_replace', profile);
						$(this).attr('name', name);
					}
				}
			});

		});
	});
}

function toggleStoreSelections(curState) {
	$(".multistore_checkbox").prop('checked', curState);
}

function preventDefault(e) {
	e.preventDefault();
}

function toggleFeedStatus() {
	$.each($('input.feed_url'), function () {
		$(this).prop('disabled', $(this).closest('.field_content').find('.settings_enable').val() == 0);
	});
	return false;
}

function toggleOnChangeCache() {
	$.each($('.settings_cache'), function () {
		$(this).closest('table.settings').find('.hideCache').toggle($(this).val() == 'Yes');
			if($(this).closest('table.settings').find('.settings_timeout').val() === '') {
				$(this).closest('table.settings').find('.settings_timeout').val(2);
			}
	});

	return false;
}

function setCacheStatus() {
	$('.settings_cache').each(function(i, val) {
		cache_enabled = ($(this).val() === 'Yes');
		if (cache_enabled)
			cache_status_text = 'on';
		else
			cache_status_text = 'off';

		var $cache_info_el = $(this).closest('.field_content').find('.cache-status');
		$cache_info_el.text(cache_status_text);
		$cache_info_el.closest('.cache-info').toggleClass('enabled', cache_enabled);
	});
}

$(document).ready(function() {

	$('#ajaxSpinner').hide(); // Init hidden

	$('#content').on('change', '.name_map_container > .name_map_enabled', function() {
		$(this).closest('.name_map_container').find('.name_map_list').toggle($(this).val() == '1');
	});

	$('#content').on('change', '.settings_cache', function() {
		$(this).closest('table').find('.hideCache').toggle($(this).val() == 'Yes');
		setCacheStatus();
	});

	$('#content').on('click', '.btn-save-feed', function() {
		saveFeedsAjax();
	});

	//load existing feeds
	var existingFeeds = new Array();
	//get each existing feed values for loading
	<?php foreach($preset_feeds as $name => $value) {
		echo 'existingFeeds["' .$name . '"] = "' . $value . '";' ."\n";
	} ?>
	//load each existing feed
	for(var key in existingFeeds) {
		createFeed(key, existingFeeds[key], 0);
	}

	//set content field values
	$(".field_text").each(function(index) {
		$(this).val(encodedName($(this).val()));
	});


    $( "#dialog-form" ).dialog({
    	autoOpen: false,
        resizable: false,
        height: 300,
        width: 350,
        modal: true,
        position: { my: "center", at: "center", of: '#content' },
        buttons: {
        	"Create a new feed": function() {
            	bValid = true;
            	validName = validateName($('#feed_name').val());

            	if(validName != '') {
					if(typeof existingFeeds[encodedName($('#feed_name').val())] == 'undefined') {
		                if ( bValid ) {
		                	existingFeeds[encodedName($('#feed_name').val())] = 1;
		                	createFeed($( "#feed_profile" ).val(), $('#feed_name').val(), 1, function() {
		                		saveFeedsAjax();
		                	});
		                	$( this ).dialog( "close" );
		                }
					} else {
						updateTips('This feed name already exists');
					}
				} else {
					updateTips('Please enter a valid Feed Name');
				}
        	},
       		Back: function() {
        		$( this ).dialog( "close" );
        	}
    	},

    });

	//dialog for Getting the Feed URL
    $( "#dialog-get-feed" ).dialog({
    	autoOpen: false,
	    resizable: false,
	    height: 200,
	    width: 600,
	    modal: true,
	    position: { my: "center", at: "center", of: '#content' },
	    buttons: {
	    	Back: function() {
	        	$( this ).dialog( "close" );
	        }
	    }
	});

	//When the feeds are added, attach dialog click
	$('#content').on('click','.get_feed_modal', function() {
		$( "#dialog-get-feed" ).dialog( "open" );
		$('.feed_url_dialog').val($(this).data('url'));
	});

    //changing the name of a feed
    $('#content').on('click','.profile_name',function renameFeed(){
		profile_h2  = this;
		profile_name = $(this).text();
		item = $(this).closest('.field_content');
		$('#rename').val('');

		$( "#dialog-rename" ).dialog({
		    resizable: false,
		    height:180,
		    modal: true,
		    position: { my: "center", at: "center", of: '#content' },
		    buttons: {
		    	"Save": function() {
 		    		//Grab the value of the  selected Feed
 		    		new_name   = encodedName($('#rename').val());

                                if(new_name != '') {
 		    			$(profile_h2).text(new_name);
	 		    		if(typeof existingFeeds[new_name] == 'undefined') {

	 		    			//Setting up my URL then saving it
	 		    			match = '/admin/';
							url = document.URL;
				 			url = url.substring(0, url.indexOf(match));
				 			url += "/index.php?route=feed/any_feed_pro&name=" + encodedName(new_name);

				 			var anchor_feed_url = $(item).find('a.feed_url');
				 			var new_name_to_use = getNewName(anchor_feed_url.attr('name'),new_name);

	 		    			anchor_feed_url.attr('href',url);
	 		    			anchor_feed_url.attr('name',new_name_to_use);

	 		    			$(item).find('input.feed_url').attr('value',url);
	 		    			$(item).find('.replace_me').val(new_name);

	 		    			item.find(':input').each(function updateNames(){

								if ($(this).is("[name]")) {

									newName = getNewName($(this).attr('name'),new_name);
									existingFeeds[new_name] = 1;
									$(this).attr('name',newName);
								}
							});
                                                        saveFeedsAjax();
                                                        $( this ).dialog( "close" );
						} else {
							updateTips('This feed name already exists');
						}
					} else {
						updateTips('Please enter a valid Feed Name');
					}
		        },
		        Back: function() {
		        	$( this ).dialog( "close" );
		        }
		    }
		});
	});

    $( "#create-feed" ).on('click', function() {
    	$('#feed_name').val('');
        $( "#dialog-form" ).dialog( "open" );
    });

	tips = $( ".validateTips" );

	function getNewName(oldName, new_name) {
		regex_profile_name = /^[a-z_]*\[/g;
		matchedName = oldName.match(regex_profile_name);

		//Im putting the bracket in because i need to add the bracket so  It matches the HTML pattern
		name = oldName.replace(matchedName, new_name + '[');
		return name;
	}

	function updateTips( t ) {
		tips
             .text( t )
             .addClass( "ui-state-highlight" );
         setTimeout(function() {
             tips.removeClass( "ui-state-highlight", 1500 );
         }, 500 );
    }

    addedFeed();

	//add portlet classes for source portlets
    $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
         .find( ".portlet-header" )
             .addClass( "ui-widget-header ui-corner-all" )
             .prepend( "<i class='ui-icon-triangle ui-icon-triangle-1-e fa fa-chevron-down'></i><span class='fa fa-times'></span>");

    // Find closest portlet and expand toggle visibility for portlet-content
	function portletToggleExpand() {
		$(".field-open").removeClass("field-open");
    	var parent_portlet = $( this ).parents(".portlet:first");
        var portlet_content = parent_portlet.find( ".portlet-content" );
        portlet_content.slideToggle();
        if (portlet_content.is(':visible'))
    		parent_portlet.addClass("field-open");
    	parent_portlet.find('.ui-icon').toggleClass( "ui-icon-triangle-1-s" ).toggleClass( "ui-icon-triangle-1-e" );
    }

	function focusFieldContent() {
		$(".focussed").removeClass('focussed');
		$(this).addClass('focussed');
	};

	$('#form').on('click', '.field_content', focusFieldContent);
	$('#form').on('click', '.field_content .portlet-header', portletToggleExpand);

	//create the field portlets for field information
    $( ".portlet-header .ui-icon-triangle" ).click(portletToggleExpand);

    function bindRemoveFieldButton() {
    	$( ".field_content  .fa-times" ).unbind('click').on('click', function() {
			$( this ).closest('.portlet').remove();
        });
    }

	function addedFeed() {
		toggleFeedStatus();
		toggleOnChangeCache();
		setCacheStatus();


		//create sortable list of fields
	    $( ".field_list" ).each( function() {
	    	$(this).sortable({
				containment : $(this),
    		 	receive: function(event, ui) {
			        ui.helper.first().removeAttr('style'); // undo styling set by jqueryUI
					bindRemoveFieldButton();
			    },
	        	axis: "y",
	        	helper: "original",
	        	placeholder: "ui-state-highlight",
	    	});
		});

	    //create the field source sortable list
	   $( ".feed_source" ).draggable({
		   	connectToSortable: ".field_list",
		    helper: function(event) {
		    	var cloned_field = $(this).clone();
		    	cloned_field.removeClass('field-open').css('height', $(this).height()).css('width', $(this).width());
		      	return cloned_field;
		    },
		    cursor: "move",
		    stop:function(event, ui){
			   	$(':input').mousedown(function(e){ e.stopPropagation(); });
					updateFieldNames();
	    	}
	    });

		$( ".field_content:last .portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
	    .find( ".portlet-header" )
	        .addClass( "ui-widget-header ui-corner-all" )
	        .prepend( "<span class='ui-icon-triangle ui-icon-triangle-1-e fa fa-chevron-down'></span>");
	    $( ".field_content:last .portlet .portlet-header:not(.feed-settings)" ).prepend("<span class='fa fa-times'></span>");


		//remove item from list
		bindRemoveFieldButton();

		//dialog box for deleting profiles
		$( ".remove" ).unbind("click").on("click", function() {
			//grab the current feed being removed
			item = this;
		    $( "#dialog-confirm" ).dialog({
			    resizable: false,
			    height:180,
			    modal: true,
			    position: { my: "center", at: "center", of: '#content' },
			    buttons: {
			    	"Delete this feed": function() {
	 		    		//remove the feed from the current feed list
	 		    		profile = encodedName($( item ).closest(".feed-header").find(".remove:first").html());
	 		    		delete existingFeeds[encodedName(profile)];
					$( item ).parents(".field_content:first").remove();
			        	$( this ).dialog( "close" );
                                        saveFeedsAjax();
			        },
			        Back: function() {
			        	$( this ).dialog( "close" );
			        }
			    }
			});
		});


		$('a.feed_url').unbind('click').on('click', function(e) {
			var feed_disabled = $(this).closest('.field_content').find('.settings_enable').val() == 0;
			if (feed_disabled)
			 	e.preventDefault();
		});

	//When a feed is added I am attaching a tooltip to it
	$('.profile_name').tooltip({
		position: { my: "left bottom", at: "left top-5"},
	});

		updateFieldNames();

	}

	function sync_namemap_list(namemap_type, map_list) {
		var name_map = [];
		<?php
		foreach ($name_map as $name => $list) { ?>
			name_map['<?php echo $name; ?>'] = [
				<?php foreach ($list as $item) { ?>
					'<?php echo addslashes(trim($item["name"])); ?>',
				<?php } ?>
			];
		<?php } ?>

		var cur_names = [];

		$.each(map_list, function(index, val) {
			cur_names.push(val['original']);
		});

		// Attribute names are not case-sensitive in html, manually capitilize strings to be safe
		namemap_type = namemap_type.charAt(0).toUpperCase() + namemap_type.slice(1);

		if (Object.keys(map_list).length) {
			name_map[namemap_type].map(function(name) {
				if ($.inArray(name, cur_names) === -1) {
					cur_names.push(name);
					map_list[Object.keys(map_list).length +1 ] = {'new': '', 'original': name};
				}
			});
		}

		return map_list;

	}

	function createFeed (profile, name, preset, callback) {
                if(!name)
                    return;
                
		var json_url = 'index.php?route=feed/any_feed_pro/getProfile&feed='+ profile +'&preset=' + preset + '&token=<?php echo $token ?>';
		var json = $.getJSON(json_url, function(data) {
		<!-- Feed URL -->
		match = '/admin/';
		url = document.URL;
		url = url.substring(0, url.indexOf(match));
		url += "/index.php?route=feed/any_feed_pro&name=" + encodedName(name);                                


		var copyurl = 'index.php?route=feed/any_feed_pro/duplicate&name=' + encodedName(name) + '&token=<?php echo $token ?>';
		var feedSettingurl = 'index.php?route=feed/any_feed_pro/editFeedSettings&name=' + encodedName(name) + '&token=<?php echo $token ?>';
		var feedDelete = 'index.php?route=feed/any_feed_pro/deleteFeed&name=' + encodedName(name) + '&token=<?php echo $token ?>';

		html = '<button class="accordiona" id="accordion_' + name + '" onclick="manageAccordian(\'' + name + '\')"><span class="name-label"><i class="fa fa-rss-square" aria-hidden="true"></i>&nbsp;&nbsp;' + prettyName(name) + '</span></button><div id="feed_div_' + name + '" class="field_content panela" style="width: 100%;">';
			addedDefaults = false;
			 $.each(data, function(key, val) {
					switch(key) {
						case 'name':
							html += '<div class="portlet static">';
							html += '<div class="feed-header">';

							html += "<a class='feed_url feed_header_buttons' name='feed_name_replace[settings][feed_url]' href='javascript:void(0)' onclick=\"copyFile('"+ feedSettingurl +"')\">";
                                                        html += "<i class='fa fa-cogs fa-lg' aria-hidden='true'></i> Settings</a>";
							html += "<a class='feed_url feed_header_buttons' name='feed_name_replace[settings][feed_url]' href='javascript:void(0)' onclick=\"copyFile('"+ copyurl +"')\">";
                                                        html += "<i class='fa fa-clone fa-lg' aria-hidden='true'></i> Duplicate</a>";
							html += "<a class='feed_url feed_header_buttons' name='feed_name_replace[settings][feed_url]' href='javascript:void(0)' onclick=\"exportFile('"+ url +"')\">";
							html += "<i class='fa fa-external-link fa-lg'></i> Export Feed</a>";
							html += "<span class='get_feed_modal feed_header_buttons' name='feed_name_replace[settings][feed_get_url]' data-url='" + url + "'>";
							html += "<i class='fa fa-external-link fa-lg'></i> Get Feed URL </span>";
							html += '<a class="feed_header_buttons" href="javascript: void(0)" onclick=\'deleteFeed("'+ feedDelete +'")\'><span><i class="fa fa-trash-o fa-lg"></i> Delete Feed </span><input class="replace_me" type="hidden" name="feed_name_replace[name]" value="' + encodedName(name) + '"/>';
							// html += '<span class="remove feed_header_buttons"><i class="fa fa-trash-o fa-lg"></i></span><input class="replace_me" type="hidden" name="feed_name_replace[name]" value="' + encodedName(name) + '"/>';
							html += "<a class='btn-save-feed feed_header_buttons'><i class='fa fa-save fa-lg'></i> Save</a>";

							html += '</div>';

							html += '<div class="feed-title">';
                                                        html += '<h2 class="profile_name hover">' + prettyName(name) + '</h2>';
							html += '</div>';

							break;
						case 'settings':
							settings = JSON.parse(val);
							html += '<div class="portlet-header feed-settings ui-corner-all">Feed Settings <span class="cache-info">(Cache is <span class="cache-status"></span>)<span style="padding-left:15%;">[Click on "Settings" button to edit this feed]</span></span></div><div class="portlet-content"><table class="settings">';

							<!-- Feed Enable/Disable -->
							html += "<tr>";
                                                        html += "<td><label class='enable' for='enable'>Feed Status: </label></td>";
							html += "<td><select class='settings_enable settings_" + encodedName(name) + "' name='feed_name_replace[settings][enable]'>";
							var feed_statuses = [
								{'text': 'Enabled', 'val': 1},
								{'text': 'Disabled', 'val': 0},
							];
							for (index = 0; index < feed_statuses.length; ++index) {
								html += "<option value='"+ feed_statuses[index].val +"'";
								if(settings.enable == feed_statuses[index].val)
									html += " selected='true'";
								html += ">"+feed_statuses[index].text+"</option>";
							}
							html += "</select></td>";
							html += "</tr>";

							<!-- Output Disabled products -->
							html += "<tr>";
                                                        html += "<td><label class='export_disabled' for='enable'>Export Disabled Products: </label></td>";
							html += "<td><select class='settings_export_disabled_products settings_" + encodedName(name) + "' name='feed_name_replace[settings][export_disabled_products]'>";
							var export_disabled_options = [
								{'text': 'Yes', 'val': 1},
								{'text': 'No', 'val': 0},
							];
							for (index = 0; index < export_disabled_options.length; ++index) {
								html += "<option value='"+ export_disabled_options[index].val +"'";
								if(settings.export_disabled_products == export_disabled_options[index].val)
									html += " selected='true'";
								html += ">"+export_disabled_options[index].text+"</option>";
							}
							html += "</select></td>";
							html += "</tr>";

							<!-- Feed Output Type -->
                                                        html += "<tr>";
							html += "<td><label for='type'>Feed Type: </label></td>";
							html += "<td><select class='settings_feedtype settings_" + encodedName(name) + "' name='feed_name_replace[settings][type]'>";
							var types = ['CSV', 'XML', 'TXT'];
							for (index = 0; index < types.length; ++index) {
								html += "<option value='"+ types[index] +"'";
								if(settings.type == types[index]) {
									html += " selected='true'";
								}
								html += ">"+types[index]+"</option>";
							}
							html += "</select></td></tr>";

						    if(!addedDefaults) {

								<!-- CSV Delimiter -->
								html += "<tr>";
								html += "<td><label class='CSV' for='delimiter'>Delimiter: </label></td>";
								html += "<td><select class='CSV' name='feed_name_replace[settings][delimiter]' class='delimiter'><br class='CSV' />";
                                var delimiters = {
                                	',': ',',
                                	':': ':',
                                	';': ';',
                                	'|': '|',
                                	'^': '^',
                                	'Tab': '\t',
                            	};
                            	$.each(delimiters, function (option_name, option_key) {
                            		html += "<option value='"+ option_key +"'";
									if(settings.delimiter == option_key) {
										html += " selected='true'";
									}
									html += ">"+option_name+"</option>";
                        		});
								html += "</select></td>";
								html += "</tr>";

                                <!-- CSV Filename -->
                                html += "<tr>";
                                html += "<td><label class='CSV' for='filename'>Filename: </label></td>";
                                html += "<td><input class='CSV' name='feed_name_replace[settings][filename]' placeholder='opencart_products.csv' type='text' value='";
                                if(settings.filename) {
                                    html += settings.filename;
                                }
                                html += "'/></td>";
                                html += "</tr>";

                                <!-- CDATA -->
                                html += "<tr>";
								html += "<td><label class='XML' for='cdata'>Use cdata: </label></td>";
								html += "<td><select class='XML settings_cdata settings_" + encodedName(name) + "' name='feed_name_replace[settings][cdata]'>";
								var cdata_options = [
									{text: 'Yes', val: 1},
									{text: 'No', val: 0},
								];
								for (index = 0; index < cdata_options.length; ++index) {
									html += "<option value='"+ cdata_options[index].val +"'";
									if(parseInt(settings.cdata) === cdata_options[index].val) {
										html += " selected='true'";
									}
									html += ">"+cdata_options[index].text+"</option>";
								}
								html += "</select></td>";
                                html += "</tr>";

								<!-- XML Root Tag -->
								html += "<tr>";
                                html += "<td><label class='XML' for='root_tag'>Root tag: </label></td>";
                                html += "<td><input class='XML' name='feed_name_replace[settings][root_tag]' type='text' placeholder='any_feed_pro_product_list' value='";
                                if(settings.root_tag) {
                                    html += settings.root_tag;
                                }
                                html += "'/></td>";
                                html += "</tr>";

                                <!-- Currency -->
                                html += "<tr>";
                                html += "<td><label for='currency'>Currency: </label></td>";
                            	html += "<td><select name='feed_name_replace[settings][currency]'>";
                                <?php
                                    $currencies['ALL'] = array(
                                        "code" => "ALL",
                                        "title" => "All Currency"
                                    );
                                ?>
                            	<?php foreach ($currencies as $currency_name => $currency_data) { ?>
                        			html += "<option value='<?php echo $currency_name ?>'";
                        			if ((settings.currency && settings.currency == <?php echo "'".$currency_name."'"; ?>) ||
                        				(!settings.currency && <?php echo ($default_currency == $currency_name ? 1 : 0); ?>)) {
                        				html += " selected='selected'";
                        			}
                        			html += "><?php echo ($currency_name == $default_currency ? '(default) ' : '') . $currency_name?></option>";
                            	<?php } ?>
								html += "</select></td>";
								html += "</tr>";

								<!-- Language -->
								html += "<tr>";
								html += "<td><label for='language'>Language: </label></td>";
                            	html += "<td><select name='feed_name_replace[settings][language]'>";
                                <?php
                                    $languages[0] = array(
                                          "language_id" => 0,
                                          "name" => "All Languages"
                                    );
                                ?>
                                
                            	<?php foreach ($languages as $language_name => $language_data) { ?>
                        			html += "<option value='<?php echo $language_data['language_id'] ?>'";
                        			if ((settings.language && settings.language == <?php echo "'".$language_data['language_id']."'"; ?>) ||
                        				(!settings.language && <?php echo ($default_language == $language_name ? 1 : 0); ?>)) {
                        				html += " selected='selected'";
                        			}
                        			html += "><?php echo ($language_data['name'] == $languages[$default_language]['name'] ? '(default) ' : '') . $language_data['name']?></option>";
                            	<?php } ?>
								html += "</select></td>";
								html += "</tr>";


								<!-- Multi Stores -->
								html += "<tr>";
							    <?php if (count($store_selections)) { ?>
									if (!settings.store)
										settings.store = [];
									html += "<td><label class='shops'>Shops: </label></td>";
									html += "<td>";
									html += "<div><input type='checkbox' class='multistore_checkbox' id='checkbox_store_0' onclick='selectStoreCheckbox(0)' name='feed_name_replace[settings][store][Default]' value='0'";
									if (settings.store.Default || Object.keys(settings.store).length === 0)
										html += "checked='true'";
									html += "><label>Default</label></div>";
									<?php foreach ($store_selections as $sto) { ?>
										html += "<div><input type='checkbox' class='multistore_checkbox' id='checkbox_store_<?php echo $sto['store_id']; ?>' onclick='selectStoreCheckbox(<?php echo $sto['store_id']; ?>)' name='feed_name_replace[settings][store][<?php echo $sto['name']; ?>]' value='<?php echo $sto['store_id']; ?>'";
										if (settings.store['<?php echo $sto['name'] ?>'])
										 	html += "checked='true'";
										html += "><label><?php echo $sto['name']; ?></label></div>";
									<?php } ?>
							    <?php }	else { ?>
										html += '<input type="hidden" name="feed_name_replace[settings][store][]" value="0"/>';
								<?php } ?>
								html += "</td></tr>";

								<!-- CACHE -->
                                html += "<tr>";
								html += "<td><label for='cache'>Use Cache: </label></td>";
								html += "<td><select class='settings_cache settings_" + encodedName(name) + "' name='feed_name_replace[settings][cache]'>";
								var cache_options = ['No','Yes'];
								for (index = 0; index < cache_options.length; ++index) {
									html += "<option value='"+ cache_options[index] +"'";
									if(settings.cache === cache_options[index]) {
										html += " selected='true'";
									}
									html += ">"+cache_options[index]+"</option>";
								}
								html += "</select></td>";
                                html += "</tr>";

                                <!-- Help Text for Cache -->
                                html += '<tr>';
                                html += '<td></td>';
                                html += '<td><span class="help-block">A new feed will be generated if a cached file is older than the timeout value.</span></td>';
                                html += '</tr>';

                                <!-- CACHE TIMEOUT -->

                                html += "<tr class='hideCache'>";
								html += "<td><label for='timeout'>Cache Timeout: </label></td>";
								html += "<td><input class='settings_timeout settings_" + encodedName(name) + "' onchange='saveFeedsAjax();' name='feed_name_replace[settings][timeout]' type='number' min='0' max='60' value='"+settings.timeout+"'> mins</td>";
                                html += "</tr>";

                                function getRealPathURL() {
                                	var path;

                                	<?php $path = str_replace('\\', '/', DIR_CATALOG);?>
                                	path = "<?php echo PHP_BINDIR . '/php ' . $path.'..' . '/cron_feed.php '?>";
                                	return path;
                                };

                                <!-- CRON CACHE -->
                                html += "<tr class='hideCache'>";
								html += "<td><label>Cron Command: </label></td>";
								html += "<td><input class='settings_" + encodedName(name) + "' type='text' readonly='readonly' onClick='this.setSelectionRange(0, this.value.length)' value='" + getRealPathURL() + "" + encodedName(name) + "'></td>";
                                html += "</tr>";

								<!-- Help Text for Cron Cache -->
                                html += '<tr class="hideCache">';
                                html += '<td></td>';
                                html += '<td><span class="help-block">If you have a large feed, create a Cron Task to automatically create a cached version.</span></td>';
                                html += '</tr>';

								addedDefaults = true;
							}
							html += '</table></div></div>';
							break;
						case 'fields':
							html += '<h2 class="field_heading">Fields</h2>';
							html += '<div class="field_list"><table><tr><td class="spacer">&nbsp;<td></tr></table>';
							fields = JSON.parse(val);
							var fields_length = 0;
							if (fields !== null) {
								fields_length = fields.length;
							}
							var sorted_fields = Array.apply(null, {length: fields_length});
							for (var key in fields) {
								for (var index in fields[key].settings) {
									var sIdx = parseInt(fields[key].settings[index].sort_order);
									sorted_fields[sIdx] = {};
									sorted_fields[sIdx][key] = {
										settings: [fields[key].settings[index]]
									};
								}
							}
							/*
							var fieldNums = Array();
							$.each(fields, function getKeys(key, val) {
								fieldNums[key] = 0;
							});
							fieldNums = $.unique(fieldNums);
							*/
							for (var index in sorted_fields){
								var key = Object.keys(sorted_fields[index])[0];
								for (var fieldNum in sorted_fields[index][key]['settings']) {
									html += '<div class="portlet">';
									html += '<div class="portlet-header"><span class="field_name">' + key + '</span></div>';
									html += '<div class="portlet-content">';
									delete sorted_fields[index][key]['settings'][fieldNum]['sort_order'];
									for (var value in sorted_fields[index][key]['settings'][fieldNum]) {
										if (sorted_fields[index][key]['settings'][fieldNum].hasOwnProperty(value)) {
											field_settings = sorted_fields[index][key]['settings'][fieldNum][value];

											if(field_settings['type'] == 'rule') {
												html += "<label>" + value + ":</label>";
												html += "<button class='btn btn-link' onclick='javascript:addRule(\""+key+"\", $(this).siblings(\".rule\").length, this);updateFieldNames();return false;'><i class='fa fa-plus'></i></button>"
												html += "<br class='rules_below' />";

												for(var rule_num in field_settings['rules']) {
													html += "<span class='rule'>If " + key + " ";
													html += "<select name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][rules]["+rule_num+"][rule_comparator]'>";
													<?php foreach ($rule_comparisons as $comparator) { ?>
														html += "<option value='<?php echo $comparator['val']; ?>'"
														if(field_settings['rules'][rule_num]['rule_comparator'] == '<?php echo $comparator["val"]; ?>') {
															html += " selected";
														}
														html += "><?php echo $comparator['name']; ?></option>";
													<?php } ?>
													html += "</select>";

													html += "<input type='text' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][rules]["+rule_num+"][rule_comparator_value]' value='" + field_settings['rules'][rule_num]['rule_comparator_value'] + "'>";

													html += " Export ";

													html += "<select class='export_type' onchange='updateRuleExportInput(this)' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][rules]["+rule_num+"][rule_export_type]'>";
													<?php foreach ($rule_export_types as $export_type) { ?>
														html += "<option value='<?php echo $export_type['val']; ?>'";
														if (field_settings['rules'][rule_num]['rule_export_type'] == '<?php echo $export_type['val'] ?>')
															html += " selected";
														html += "><?php echo $export_type['name'] ?></option>";
													<?php } ?>
													html += "</select>";

													html += "<select class='math rule_export'";
								        			if (field_settings['rules'][rule_num]['rule_export_type'] != 'math')
								        				html += " style='display:none;'";
								        			html += ">";
							    					<?php foreach ($math_operators as $k => $field) { ?>
							    						html += "<option value='<?php echo $field; ?>'";
							    						if (field_settings['rules'][rule_num]['rule_export_type'] == 'math' && field_settings['rules'][rule_num]['math_operator'] == '<?php echo $field; ?>')
							    							html += " selected"
							    						html += "><?php echo $field; ?></option>";
													<?php } ?>
													html += "</select>";

								        			html += "<input class='rule_export' type='text'";
								        			if (field_settings['rules'][rule_num]['rule_export_type'] != 'txt' && field_settings['rules'][rule_num]['rule_export_type'] != 'math'  && field_settings['rules'][rule_num]['rule_export_type'] != 'append' )
								        				html += " style='display:none;'";
								        			else
								        				html += " value='" + field_settings['rules'][rule_num]['rule_export'] + "'";
								        			html += ">";

								        			html += "<select class='rule_export'";
								        			if (field_settings['rules'][rule_num]['rule_export_type'] != 'slct')
								        				html += " style='display:none;'";
								        			html += ">";
							    					<?php foreach ($rule_export_fields as $k => $field) { ?>
							    						html += "<option value='<?php echo $field; ?>'";
							    						if (field_settings['rules'][rule_num]['rule_export_type'] == 'slct' && field_settings['rules'][rule_num]['rule_export'] == '<?php echo $field; ?>')
							    							html += " selected"
							    						html += "><?php echo $field; ?></option>";
													<?php } ?>
													html += "</select>";

													html += "<input type='hidden' class='rule_export_value' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][rules]["+rule_num+"][rule_export]'>";
													html += "<input type='hidden' class='rule_math_operator' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][rules]["+rule_num+"][math_operator]'>";
													html += "<button class='btn btn-link' onclick='javascript:$(this).closest(\".rule\").remove();return false;'><i class='fa fa-trash-o'></i></button>";
													html += "</span>";
												}

											}
											if(field_settings['type'] == 'text') {
												html += "<label for="+field_settings['name']+"'>" + value + ":</label>";
												html += "<br /><input name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][value]' type='text' class='field_text' value='" + field_settings['value'] + "'/>";
											}

											if(field_settings['type'] == 'unit') {
												html += "<label for="+field_settings['name']+"'>" + value + ":</label>";
												html += "<select name='feed_name_replace[fields]["+key+"][settings]["+fieldNum+"]["+value+"][location]'>";
												var unit_options = ['before', 'after', 'none'];
												unit_options.map(function (e, i) {
													html += "<option value='"+e+"'";
													if (field_settings['loaction'] === e) {
														html += ' selected="selected"';
													}
													html += ">"+e+"</option>";
												});
												html += "</select>";
												html += "<input type='text' name='feed_name_replace[fields]["+key+"][settings]["+fieldNum+"]["+value+"][value]' placeholder='unit symbol' value='"+field_settings['value']+"'/>";
											}

											if(field_settings['type'] == 'checkbox') {
												html += "<label for="+field_settings['name']+"'>" + value + "</label>";
												html += "<input name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][value]' type='checkbox' value='1'";
												if(field_settings['value'] == '1') {
													html += ' checked=yes';
												}
												html += "/>";
											}

											var select_options = {};
											<?php foreach ($select_options as $option_name => $options) { ?>
													select_options['<?php echo $option_name; ?>'] = {};
													<?php foreach ($options as $key => $opt_name) { ?>
														select_options['<?php echo $option_name; ?>']['<?php echo $key; ?>'] = '<?php echo $opt_name; ?>';
													<?php } ?>
											<?php } ?>

											if(field_settings['type'] == 'select') {
												html += "<label for="+field_settings['name']+"'>" + value + "</label>";
												html += "<select name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][value]'>";

												$.each(select_options[field_settings['name']], function (option_key, option_text) {
													var selected_text = '';
													if (option_key == field_settings['value']) {
														selected_text = 'selected';
													}
													html += "<option value='"+option_key+"' "+selected_text+">";
													html += option_text;
													html += "</option>";
												});
												html += "</select>";
											}

											if (field_settings['type'] == 'customer_group') {
												html += "<label for="+field_settings['name']+"'>" + value + "</label>";

												html += '<select name="feed_name_replace[fields]['+key+'][settings][field_num_replace]['+value+'][value]">';
												<?php foreach ($customer_groups as $customer_group) { ?>
                                    				html += '<option value="<?php echo $customer_group['customer_group_id']; ?>"';
                                    				if (field_settings['value'] === '<?php echo $customer_group['customer_group_id']; ?>')
                                    					html += 'selected';
                                    				html += '>';
                                    				html += '<?php echo $customer_group['name']; ?>';
                                        			html += '</option>';
                            					<?php } ?>
                            					html += '</select>';
											}

											if(field_settings['type'] == 'name_map') {
												html += "<label for="+field_settings['name']+"'>" + value + "</label>";

												html += "<div class='name_map_container'>"+
												'<select class="name_map_enabled" name="feed_name_replace[fields]['+key+'][settings][field_num_replace]['+value+'][enabled]">'+
			            							'<option value="0">Default</option>'+
			            							'<option value="1"';
		            							if (field_settings['enabled'] == '1')
													html += ' selected="true"';
			            						html += '>Custom</option>'+
			        							'</select>'+
    											"<div class='name_map_list c'";
    											if (field_settings['enabled'] == '1')
    												html += 'style="display:block"';
    											html += ">"+
	            								'<table>'+
	            									'<tr>'+
			            							'<td>Existing '+field_settings['name']+'</td>'+
													'<td>New Name</td>'+
													'</tr>';
												if (typeof field_settings['names'] === 'undefined') {
													field_settings['names'] = {};
												}

												$.each(sync_namemap_list(field_settings['name'], field_settings['names']), function(index, name_val) {
													html += '<tr>'+
		            								'<td>'+
            										'<input class="origName" type="text" readonly="readonly" '+
            										'name="feed_name_replace[fields]['+key+'][settings][field_num_replace]['+value+'][names]['+index+'][original]" '+
            										'value="'+name_val['original']+'">'+
        											'</td>'+
        											'<td>'+
													'<input class="newName" type="text" '+
													'name="feed_name_replace[fields]['+key+'][settings][field_num_replace]['+value+'][names]['+index+'][new]" '+
													'placeholder="'+name_val['original']+'"'
													if (name_val['new']) {
														html += 'value="'+name_val['new']+'"';
													}
													html += '>'+
													'</td>'+
													'</tr>';
												});
												html += '</table>'+
												'</div>'+
												'</div>';
											}

											html += "<input type='hidden' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][type]' class='field_text num_reference' value='" + field_settings['type'] + "'/>";
											html += "<input type='hidden' name='feed_name_replace[fields]["+key+"][settings][field_num_replace]["+value+"][name]' class='field_text' value='" + field_settings['name'] + "'/>";
											html += "<br />";
										}
									}
									html += '</div>';
									html += '</div>';
								}
							}
							html += '</div>';
							break;
					}
			  });
			html += "</div>";
			html = html.replace(/feed_name_replace/g, encodedName(name));
			$(".field_content:last").after(html);
			updateText(encodedName(name));
			addedFeed();
		}).done(function() {
			typeof callback === 'function' && callback();
		});
                
               // setAccordianOnDiv();
	}
});
</script>
<?php echo $footer; ?>


<script type="text/javascript">
    function copyFile(url) {
        window.location.href = url;
    }
    
    function exportFile(url) {
        window.location.href = url;
    }
    
    function manageAccordian(name) {
    	var nameObj = document.getElementById("feed_div_" + name);
    	var accordianObj = document.getElementById("accordion_" + name);
    	accordianObj.classList.toggle("activea");
       	if (nameObj.style.display === "block") {
        	   nameObj.style.display = "none";
    	 } else {
        	   nameObj.style.display = "block";
       	}    
    }
    
    function selectStoreCheckbox(id) {
        if($('#checkbox_store_' + id).prop('checked')){
            $('#checkbox_store_' + id).attr('checked', true);
            // $('#checkbox_store_' + id).prop('checked');            
        }else{
            $('#checkbox_store_' + id).attr('checked', false);
        }
    }
    
    
    function deleteFeed(url) {
        if(confirm("Are you sure ?")) {
            window.location.href = url;
        }
    }
     
</script>
