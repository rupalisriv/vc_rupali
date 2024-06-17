<?php

class PatientHelper
{
    const HOSPITAL_LATITUDE = '64.4811';
    const HOSPITAL_LONGITUDE = '33.2338';

    public function getTopNPatients($n)
    {
        $patient_details = $this->getPatientDetailsFromJson();
        $patient_details = $this->getScoredPatients($patient_details);
        return $this->getNormalizedScore($patient_details,$n);
    }
    public function getPatientDetailsFromJson()
    {
        $patients_details = file_get_contents(dirname(__DIR__, 2). '/patients.json');

        return json_decode($patients_details, true);
    }

    public function getScoredPatients($patient_details)
    {
        $score_arr = [];
        foreach ($patient_details as $key => $val) {
           $distance = $this->calculateDistanceToClinic($val['location']);
           $patient_details[$key]['distanceToClinic'] = $distance;
           $score = $this->scores($patient_details[$key]);
           $patient_details[$key]['score'] = $score;
           $score_arr[$key] = $score;
        }
        array_multisort($score_arr, SORT_DESC, $patient_details);
        return $patient_details;
    }

    public function calculateDistanceToClinic($patient_location)
    {
        return sqrt(
            (($patient_location['longitude']-self::HOSPITAL_LONGITUDE)**2)
            +
            (($patient_location['latitude']-self::HOSPITAL_LATITUDE)**2)
        );
    }

    public function scores($patient)
    {
        $score = $patient['age']*.1;
        $score -= $patient['distanceToClinic']*.1;
        $score += $patient['acceptedOffers']*.3;
        $score -= $patient['canceledOffers']*.3;
        $score -= $patient['averageReplyTime']*.2;
        return $score;
    }

    public function getNormalizedScore($patient_details, $n)
    {
        $high_score = max(array_column($patient_details, 'score'));
        $low_score = min(array_column($patient_details, 'score'));
        $normalized_score_arr = [];
        foreach ($patient_details as $key => $val) {
            $normalized_score =  ((($val['score'] - $low_score)/($high_score-$low_score)) * 9) +1;
            $patient_details[$key]['normalized_score'] =  $normalized_score;
            $normalized_score_arr[$key] = $normalized_score;
        }
        array_multisort($normalized_score_arr, SORT_DESC, $patient_details);
        return array_slice($patient_details, 0, $n);
    }
}