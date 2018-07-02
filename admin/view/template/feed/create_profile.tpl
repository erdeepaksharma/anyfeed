<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href='index.php?route=feed/any_feed_pro/manageProfile&token=<?php echo $token ?>' data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
                <input type="text" name="name" value="" id="input-name" class="form-control" value="<?php echo $this->request->post['name']; ?>" />
              <?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Feed Status"; ?></label>
            <div class="col-sm-10">
              <select name="enable" id="input-enable" class="form-control">
                  <option value="1" selected="selected">Enabled</option>
                  <option value="0">Disabled</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Export Disabled Products"; ?></label>
            <div class="col-sm-10">
              <select name="export_disabled_products" id="input-export_disabled_products" class="form-control">
                  <option value="1" selected="selected">Yes</option>
                  <option value="0">No</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Feed Type"; ?></label>
            <div class="col-sm-10">
              <select name="type" id="input-type" class="form-control">
                  <option value="CSV" selected="selected">CSV</option>
                  <option value="XML">XML</option>
                  <option value="TXT">TXT</option>
              </select>
            </div>
          </div>            
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Delimiter"; ?></label>
            <div class="col-sm-10">
              <select name="delimiter" id="input-delimiter" class="form-control">
                  <option value="," selected="selected">,</option>
                  <option value=":">:</option>
                  <option value=";">;</option>
                  <option value="|">|</option>
                  <option value="^">^</option>
                  <option value=" ">Tab</option>
              </select>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-filename"><?php echo "Filename"; ?></label>
            <div class="col-sm-10">
              <input type="text" name="filename" value="" id="input-filename" class="form-control" value="<?php echo $this->request->post['filename']; ?>" />
              <?php if ($error_filename) { ?>
                    <div class="text-danger"><?php echo $error_filename; ?></div>
              <?php } ?>
            </div>
          </div>
          
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
                                <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title'] . " (" . $currency['code'] . ")"; ?></option>
                        <?php else: ?>
                                <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title'] . " (" . $currency['code'] . ")"; ?></option>
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
                <input type="checkbox" name="store[Default]" value="0" checked="checked" /> Default <br />
                <?php foreach($store_selections as $store): ?>
                    <input type="checkbox" name="store[<?php echo $store['name'] ?>]" value="<?php echo $store['store_id'] ?>" /> <?php echo $store['name'] ?> <br />
                <?php endforeach; ?>
                
                <?php if ($error_store) { ?>
                      <div class="text-danger"><?php echo $error_store; ?></div>
                <?php } ?>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo "Use Cache"; ?></label>
            <div class="col-sm-10">
              <select name="cache" id="input-cache" class="form-control">
                  <option value="No" selected="selected">No</option>
                  <option value="Yes">Yes</option>
              </select>
            </div>
          </div>                        
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>