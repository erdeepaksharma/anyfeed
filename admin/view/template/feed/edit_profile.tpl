<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <?php
            $cancelURL = 'index.php?route=feed/any_feed_pro/manageProfile&token=' . $token;
            if(isset($cancel_return_data))
                $cancelURL = $cancel_return_data;
          ?>
        <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>       
        <a href='<?php echo $cancelURL; ?>' data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
        <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">            
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo "Feed Name"; ?></label>
            <div class="col-sm-10">
                <input type="text" name="name" value="<?php echo $profile_data['name']; ?>" id="input-name" class="form-control" value="<?php echo $this->request->post['name']; ?>" />
              <?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Feed Status"; ?></label>
            <div class="col-sm-10">
              <select name="enable" id="input-enable" class="form-control">
                  <option value="1" <?php if($profile_data['settings']['enable'] == 1): ?>selected="selected"<?php endif; ?>>Enabled</option>
                  <option value="0" <?php if($profile_data['settings']['enable'] == 0): ?>selected="selected"<?php endif; ?>>Disabled</option>
              </select>
            </div>
          </div>
          
          <?php
            if(!isset($profile_data['settings']['export_disabled_products']))
                $profile_data['settings']['export_disabled_products'] = 1;
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Export Disabled Products"; ?></label>
            <div class="col-sm-10">
              <select name="export_disabled_products" id="input-export_disabled_products" class="form-control">
                  <option value="1" <?php if($profile_data['settings']['export_disabled_products'] == 1): ?>selected="selected"<?php endif; ?>>Yes</option>
                  <option value="0" <?php if($profile_data['settings']['export_disabled_products'] == 0): ?>selected="selected"<?php endif; ?>>No</option>
              </select>
            </div>
          </div>
          
          <?php
            if(!isset($profile_data['settings']['type']))
                $profile_data['settings']['type'] = "CSV";
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Feed Type"; ?></label>
            <div class="col-sm-10">
              <select name="type" id="input-type" class="form-control">
                  <option value="CSV" <?php if($profile_data['settings']['type'] == "CSV"): ?>selected="selected"<?php endif; ?>>CSV</option>
                  <option value="XML" <?php if($profile_data['settings']['type'] == "XML"): ?>selected="selected"<?php endif; ?>>XML</option>
                  <option value="TXT" <?php if($profile_data['settings']['type'] == "TXT"): ?>selected="selected"<?php endif; ?>>TXT</option>
              </select>
            </div>
          </div>            
          
          <?php
            if(!isset($profile_data['settings']['delimiter']))
                $profile_data['settings']['delimiter'] = ",";
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Delimiter"; ?></label>
            <div class="col-sm-10">
              <select name="delimiter" id="input-delimiter" class="form-control">
                  <option value="," <?php if($profile_data['settings']['delimiter'] == ","): ?>selected="selected"<?php endif; ?>>,</option>
                  <option value=":" <?php if($profile_data['settings']['delimiter'] == ":"): ?>selected="selected"<?php endif; ?>>:</option>
                  <option value=";" <?php if($profile_data['settings']['delimiter'] == ";"): ?>selected="selected"<?php endif; ?>>;</option>
                  <option value="|" <?php if($profile_data['settings']['delimiter'] == "|"): ?>selected="selected"<?php endif; ?>>|</option>
                  <option value="^" <?php if($profile_data['settings']['delimiter'] == "^"): ?>selected="selected"<?php endif; ?>>^</option>
                  <option value=" " <?php if($profile_data['settings']['delimiter'] == " "): ?>selected="selected"<?php endif; ?>>Tab</option>
              </select>
            </div>
          </div>
            
          <?php
            if(!isset($profile_data['settings']['filename']))
                $profile_data['settings']['filename'] = ucfirst($profile_data['name']);
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-filename"><?php echo "Filename"; ?></label>
            <div class="col-sm-10">
                <input type="text" name="filename" value="<?php echo $profile_data['settings']['filename']; ?>" id="input-filename" class="form-control" value="<?php echo $this->request->post['filename']; ?>" />
              <?php if ($error_filename) { ?>
                    <div class="text-danger"><?php echo $error_filename; ?></div>
              <?php } ?>
            </div>
          </div>
            
          <?php
            if(!isset($profile_data['settings']['currency']))
                $profile_data['settings']['currency'] = $default_currency;
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Currency"; ?></label>
            <div class="col-sm-10">
                <?php
                    $currencies['ALL'] = array(
                        "code" => "ALL",
                        "title" => "All Currency"
                    );
                ?>
              <select name="currency" id="input-currency" class="form-control">
                  <?php foreach($currencies as $currency): ?>
                        <?php if($default_currency == $currency['code']): ?>
                                <option value="<?php echo $currency['code']; ?>" <?php if($profile_data['settings']['currency'] == $default_currency): ?>selected="selected"<?php endif; ?>><?php echo $currency['title'] . " (" . $currency['code'] . ")"; ?></option>
                        <?php else: ?>
                                <option value="<?php echo $currency['code']; ?>" <?php if($profile_data['settings']['currency'] == $currency['code']): ?>selected="selected"<?php endif; ?>><?php echo $currency['title'] . " (" . $currency['code'] . ")"; ?></option>
                        <?php endif; ?>                       
                  <?php endforeach; ?>
              </select>
            </div>
          </div>

          <?php
          $languages[0] = array(
                "language_id" => 0,
                "name" => "All Languages"
          );
          
          
            if(!isset($profile_data['settings']['language']))
                $profile_data['settings']['language'] = $default_language;
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Language"; ?></label>
            <div class="col-sm-10">
              <select name="language" id="input-language" class="form-control">
                  <?php foreach($languages as $key => $language): ?>
                        <?php if($profile_data['settings']['language'] == $language["language_id"]): ?>
                                <option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                        <?php else: ?>
                                <option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
                        <?php endif; ?>                       
                  <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Shops"; ?></label>
            <div class="col-sm-10">
                <input type="checkbox" name="store[Default]" value="0" <?php if(array_key_exists("Default", $profile_data['settings']['store'])): ?>checked="checked"<?php endif; ?> /> Default <br />
                <?php foreach($store_selections as $store): ?>
                    <input type="checkbox" <?php if(array_key_exists($store['name'], $profile_data['settings']['store'])): ?>checked="checked"<?php endif; ?> name="store[<?php echo $store['name'] ?>]" value="<?php echo $store['store_id'] ?>" /> <?php echo $store['name'] ?> <br />
                <?php endforeach; ?>
                
                <?php if ($error_store) { ?>
                      <div class="text-danger"><?php echo $error_store; ?></div>
                <?php } ?>
            </div>
          </div>
            
          <?php
            if(!isset($profile_data['settings']['cache']))
                $profile_data['settings']['cache'] = "No";
          ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Use Cache"; ?></label>
            <div class="col-sm-10">
              <select name="cache" id="input-cache" class="form-control">
                  <option value="No" <?php if($profile_data['settings']['cache'] == "No"): ?>selected="selected"<?php endif; ?>>No</option>
                  <option value="Yes" <?php if($profile_data['settings']['cache'] == "Yes"): ?>selected="selected"<?php endif; ?>>Yes</option>
              </select>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>