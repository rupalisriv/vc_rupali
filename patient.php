<?php

require 'vendor/autoload.php';

require 'src/Helper/PatientHelper.php';

$patient = new PatientHelper();
fwrite(STDOUT, "Please enter number of patients: ");
$input = fgets(STDIN);
$patients = $patient->getTopNPatients($input);
//return patients once you have your api end points.
print_r($patients);