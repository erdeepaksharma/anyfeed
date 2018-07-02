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
            <label class="col-sm-2 control-label"><?php echo "Profiles"; ?></label>
            <div class="col-sm-10">
              <select name="preset_profile_name" id="preset_profile_name" class="form-control">
                  <?php foreach($preset_profile as $value): ?>                  
                  <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
          </div>
            
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>