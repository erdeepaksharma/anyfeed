<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
        <h1>
            <?php echo $heading_title; ?>
        </h1> 

        <div class="buttons pull-right">
            <a href='index.php?route=feed/any_feed_pro&token=<?php echo $token ?>' class="button btn btn-primary" style="background-color: coral; border-color: coral;">Back to Feed</a>
            <a href='index.php?route=feed/any_feed_pro/createProfile&token=<?php echo $token ?>' class="button btn btn-success" style="background-color: green; border-color: green;">Create New Profiles</a>
	</div>
    </div>
  </div>
  <div class="container-fluid">
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
    <!--
    <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php // echo $text_event; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo "Name"; ?></td>
                <td class="text-left"><?php echo "Filename"; ?></td>
                <td class="text-left"><?php echo "Currency"; ?></td>
                <td class="text-left"><?php echo "Languages"; ?></td>
                <td class="text-left"><?php echo "Stores"; ?></td>
                <td class="text-left"><?php echo "Type"; ?></td>
                <td class="text-left"><?php echo "Status"; ?></td>
                <td class="text-left"><?php echo "Options"; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($profileData) { ?>
              <?php foreach ($profileData as $profile): ?>
              <?php          
                $profileSettingsObject = null;
                if(!empty($profile["settings"]))
                    $profileSettingsObject = json_decode($profile["settings"], TRUE);
              ?>
                <tr>
                  <td class="text-left">
                      <?php echo $profile["name"]; ?>
                  </td>
                  
                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['filename'])? $profileSettingsObject['filename']: "-"; ?>
                  </td>
                  
                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['currency'])? $profileSettingsObject['currency']: $default_currency; ?>
                  </td>
                  
                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['language'])? $languages_by_id[$profileSettingsObject['language']]["name"]: $languages[$default_language]["name"]; ?>
                  </td>

                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['store'])? implode(", ", array_keys($profileSettingsObject["store"])): "Default"; ?>
                  </td>
                  
                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['type'])? $profileSettingsObject['type']: "-"; ?>
                  </td>
                  
                  <td class="text-left">
                      <?php echo !empty($profileSettingsObject['enable'])? "Enabled": "Disabled"; ?>
                  </td>

                  <td class="text-right">
                        <a href="index.php?route=feed/any_feed_pro/editProfile&id=<?php echo $profile['id']; ?>&token=<?php echo $token ?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="Edit Profile">
                           <i class="fa fa-pencil"></i>
                        </a>                      
                      
                        <?php $id = $profile["id"]; ?>
                        <a onclick="deleteProfile('index.php?route=feed/any_feed_pro/deleteProfileUsingId&id=<?php echo $id ?>&token=<?php echo $token ?>');" href="javascript:void(0)" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Delete Profile">
                            <i class="fa fa-minus-circle"></i>
                        </a>
                      
                        <a href="index.php?route=feed/any_feed_pro/editProfileFields&id=<?php echo $profile['id']; ?>&token=<?php echo $token ?>" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Manage Fields">
                           <i class="fa fa-cogs"></i>
                        </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 

<script type="text/javascript">
    function deleteProfile(url) {
        if (confirm("Are you sure?")) {
            window.location.href = url;
        }
    }
</script>