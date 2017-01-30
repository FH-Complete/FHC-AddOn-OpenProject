<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0">

</head>
<body>
<div class="container">
    <form method="post">
        <div class="form-group row">
            <label class="col-2 col-form-label">URL:</label>
            <input class="form-control" name="url" type="text" value="{url}">
        </div>
        <div class="form-group row">
            <label class="col-2 col-form-label">Api-Key:</label>
            <input class="form-control" name="apikey" type="text" value="{api_key}">
        </div>
        <div class="form-group row">
            <label class="col-2 col-form-label">Api-Path:</label>
            <input class="form-control" name="apipath" type="text" value="{api_path}">

        </div>
        <div class="form-group row">
            <label for="workpackage">Select type for Arbeitspakete:</label>
            <select class="form-control" name="workpackage">
                {options1}
            </select>
        </div>
        <div class="form-group row">
            <label for="milestone">Select type for Milestone:</label>
            <select class="form-control" name="milestone">
                {options2}
            </select>
        </div>
        <div class="form-group row">
            <label for="task">Select type for Task:</label>
            <select class="form-control" name="task">
                {options3}
            </select>
        </div>
        <div class="form-group row">
            <label for="projectphase">Select type for Projektphase:</label>
            <select class="form-control" name="projectphase">
                {options4}
            </select>
        </div>

        <button class="btn btn-primary" type="submit" >Config updaten</button>


    </form>
    {alert}
</div>
</body>
</html>

