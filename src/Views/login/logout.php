<?php

$sessionHelper = new \JairoJeffersont\Helpers\LoginSessionHelper();

$sessionHelper::destroySession();
header('location: ?section=login');