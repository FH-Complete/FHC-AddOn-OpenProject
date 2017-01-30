<?php
defined('BASEPATH') || exit('No direct script access allowed');
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0">

</head>
<body>
<div class="container">
    <form class="form-horizontal" method="post">
        <div class="form-group row">
            <label class="col-2 col-form-label">URL:</label>
            <input class="form-control" name="url" type="text" value="<?php echo $config['server'] ?>">
        </div>
        <div class="form-group row">
            <label class="col-2 col-form-label">Api-Key:</label>
            <input class="form-control" name="api_key" type="text" value="<?php echo $config['api_key'] ?>">
        </div>

        <br>

        <legend>
            <label>Work package type mapping</label>
        </legend>

        <div class="form-group">
            <div class="col-sm-2">FH Complete</div>
            <div class="col-sm-8">OpenProject</div>
        </div>

        <?php foreach(['Arbeitspaket', 'Milestone', 'Task', 'Projektphase'] as $key_type): ?>
            <div class="form-group">
                <label for="workpackage" class="col-sm-2 control-label"><?php echo $key_type ?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="<?php echo $key_type ?>">
                        <?php foreach (array_keys($types) as $type): ?>
                            <option <?php echo $config['type_mapping'][$key_type]['title'] == $type ? 'selected':'' ?> >
                                <?php echo $type ?>
                            </option>';
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        <?php endforeach ?>

        <br>

        <legend>
            <label>Status mapping</label>
        </legend>

        <div class="form-group">
            <div class="col-sm-2">FH Complete</div>
            <div class="col-sm-8">OpenProject</div>
        </div>

        <?php foreach(['new', 'closed'] as $key_status): ?>
            <div class="form-group">
                <label for="workpackage" class="col-sm-2 control-label"><?php echo $key_status ?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="<?php echo $key_status ?>">
                        <?php foreach (array_keys($statuses) as $status): ?>
                            <option <?php echo $config['status_mapping'][$key_status]['title'] == $status ? 'selected':'' ?> >
                                <?php echo $status ?>
                            </option>';
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        <?php endforeach ?>

        <button class="btn btn-primary" type="submit">Update</button>

    </form>
    <?php echo $alert ?>
</div>
</body>
</html>

