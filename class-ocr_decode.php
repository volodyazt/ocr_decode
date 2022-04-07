<?php
class ocr_decode {

public function __construct($filePath, $methodType) {
 
       $this->filePath = $filePath;
       $this->methodType = $methodType;

    }
	
private function curl_responce () {
if( !file_exists($this->filePath) ){
        $error_array = array(
            'code' => 'error',
            'message' => _x( 'File not found', 'OCR', 'ekush' )
        );

        wp_send_json_error( $error_array );
    }

    $fileSize = filesize($this->filePath);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $finfo = finfo_file($finfo, $this->filePath);
    $cFile = new \CURLFile($this->filePath, $finfo, basename($this->filePath));
    $data = array( "file" => $cFile, "filename" => $cFile->postname);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL             => 'https://9c81zblb46.execute-api.us-east-1.amazonaws.com/dev/' . $this->methodType,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => 'POST',
        CURLOPT_POSTFIELDS      => $data,
        CURLOPT_INFILESIZE      => $fileSize
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $response_decode = json_decode($response, true);

    return $response_decode;
}

public function getDataFromOCR () {

    if ($this->methodType == 'teudatZehut') {
            echo "ID number: ".$this->getPassportId();
        } else if ($this->methodType == 'prescription') {
            echo "Prescription start: "; 
            print_r($this->getPrescriptionStart());
            echo "Prescription end: ";
            print_r($this->getPrescriptionEnd());
            echo "Doctor: ";
            print_r($this->getDoctorName());
            echo "Prescription number: ";
            print_r($this->getPrescriptionNumber());
        } else if ($this->methodType == 'license') {
            echo "License number: ".$this->getLicenseNumber();
            echo "First name: ".$this->getLicenseFirstName();
            echo "Last name: ".$this->getLicenseLastName();
            echo "License Expiration: ".$this->getLicenseExpiration();
        } else {
            echo "Error";
        }

}


private function getPassportId () {

    $id_number_responce = $this->curl_responce();
    $id_number = $id_number_responce['ID_NUMBER'];
    if (is_null($id_number)) {
        return 'Incorrect file';
    } 
    return $id_number;
}


private function getLicenseNumber(){

    $license_number_responce = $this->curl_responce();
    $license_number = $license_number_responce['license'];
    if (is_null($license_number)) {
        return 'Incorrect file';
    } 
    return $license_number;
}

private function getLicenseFirstName(){
	$license_first_name_responce = $this->curl_responce();
    $license_first_name = $license_first_name_responce['patient']['first_name'];
        if (is_null($license_first_name)) {
        return 'Incorrect file';
    } 
    return $license_first_name;
}

private function getLicenseLastName(){
    $license_last_name_responce = $this->curl_responce();
    $license_last_name = $license_last_name_responce['patient']['last_name'];
        if (is_null($license_last_name)) {
        return 'Incorrect file';
    } 
    return $license_last_name;
}

private function getLicenseExpiration(){
    $license_last_name_responce = $this->curl_responce();
    $license_last_name = $license_last_name_responce['license_end_date'];
        if (is_null($license_last_name)) {
        return 'Incorrect file';
    } 
    return $license_last_name;
}

public function getPrescriptionStart() {

    $prescription_start_responce = $this->curl_responce();
    $prescription_start = $prescription_start_responce[0][0]['rx_pages'];

    $prescription_start_arr = [];

    foreach ($prescription_start as $value)
    {
        $prescription_start_arr [] = $value['prescription_start_date'];
    }

    return $prescription_start_arr;
}

private function getPrescriptionEnd() {

    $prescription_end_responce = $this->curl_responce();
    $prescription_end = $prescription_end_responce[0][0]['rx_pages'];

    $prescription_end_arr = [];

    foreach ($prescription_end as $value)
    {
        $prescription_end_arr [] = $value['prescription_end_date'];
    }

    return $prescription_end_arr;
}

private function getDoctorName() {

    $prescription_doctor_responce = $this->curl_responce();
    $prescription_doctor = $prescription_doctor_responce[0][0]['rx_pages'];

    $prescription_doctor_arr = [];

    foreach ($prescription_doctor as $value)
    {
        $prescription_doctor_arr [] = $value['doctor']['full_name'];
    }

    return $prescription_doctor_arr;
}

private function getPrescriptionNumber() {

    $prescription_number_responce = $this->curl_responce();
    $prescription_number = $prescription_number_responce[0][0]['rx_pages'];

    $prescription_number_arr = [];

    foreach ($prescription_number as $value)
    {
        $prescription_number_arr [] = $value['prescription_number'];
    }


    return $prescription_number_arr;

    }
}