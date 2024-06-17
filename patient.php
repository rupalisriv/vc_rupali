<?php

require 'vendor/autoload.php';

require 'src/Helper/PatientHelper.php';

$patient = new PatientHelper();
$patients = $patient->getTopNPatients(5);
print_r($patients);