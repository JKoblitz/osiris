<?php

class IDA
{
    private $authorized = false;
    public $msg = '';
    private $token = null;
    private $mail = null;
    private $institute = null;
    private $base_url = "https://mainly.api.ida.leibniz-gemeinschaft.de";

    public $user = [];
    public $last_title = '';

    function __construct($email=null, $password=null)
    {
        if ($email !== null && $password !== null){
            $msg = $this->auth($email, $password);
            if ($msg !== true) $this->msg = $msg;
            else $this->authorized = true;
        } else if (isset($_SESSION['ida-token']) && isset($_SESSION['ida-mail'])){
            $this->token = $_SESSION['ida-token'];
            $this->mail = $_SESSION['ida-mail'];
            $this->authorized = true;
            $this->institute = $_SESSION['ida-institute'];
        } else {
            $this->msg = 'Unauthorized';
        }

//         $this->base_url."/incoming/formulars";
// $this->base_url."/incoming/formulars/".$formular_url."/applications";
// $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/applications?formular_ids=".$formular_id;
// $this->base_url."/incoming/formulars/".$formular_id."/custom_fields";
// $this->base_url."/incoming/formulars/".$formular_id."/applications/".$dataset_id."/steps";
// $this->base_url."/incoming/formulars/".$formular_id."/step_tiles";
// $this->base_url."/auth/sign_in";
// // $this->base_url."/incoming/dashboard_items/".$dashboard_item_id; // request a given dashboard
// // $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/dashboard_items"; // request dashboard items for the institution
// $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/applications";
// $this->base_url."/incoming/applications/".$dataset_id."/custom_field_values";
// $this->base_url."/incoming/formulars/".$formular_url."/applications/".$dataset_slug."/previous";
// $this->base_url."/incoming/applications/".$previous_dataset_id."/custom_field_values";
// $this->base_url."/incoming/applications/".$dataset_id."/custom_field_values',".$custom_fields[$attribute_name]["id"].",'".$gris_value[0]."','".$_SESSION["ida_email"]."','".$_SESSION["ida_authentication_token"]."')", "ida_dataset_".$dataset_id." ida_step_".$custom_fields[$attribute_name]["step_id"]);
// $this->base_url."/incoming/formulars/".$formular_id."/applications/".$dataset_id."/steps/".$step_id;
    }

    function is_authorized(){
        return $this->authorized;
    }

    function auth($email, $password)
    {
        $this->authorized = false;
        $data = json_encode([
            "user" => [
                "email" => $email,
                "password" => $password
            ]
        ]);
        $url = "/auth/sign_in";
        $result = $this->request($url, 'POST', $data);

        if (isset($result['error'])) return($result['error']);
        if (!isset($result['authentication_token'])) return $result;

        $this->token = $result['authentication_token'];
        $this->mail = $result['email'];
        $this->user = $result;
        $this->institute = $result["institutions"][0]["id"];
        $this->authorized = true;
        
        $_SESSION['ida-token'] = $this->token;
        $_SESSION['ida-mail'] = $this->mail;
        $_SESSION['ida-institute'] = $this->institute;
        return true;
    }

    function check($result){
        if (isset($result['error'])){
            $this->authorized = false;
            $this->msg = $result['error'];
            return false;
        }
        return true;
    }

    function dashboard()
    {
        
        $url = "/incoming/institutions/25/dashboard_items";
        $result = $this->request($url);
        
        return $result[0] ?? false;
    }
    function formular($id)
    {
        $result = [];
        $url = "/incoming/formulars/$id/custom_fields";
        $result['custom_fields'] = $this->request($url, 'GET');

        $url = "/incoming/formulars/$id/step_tiles";
        $result['step_tiles'] = $this->request($url, 'GET');

        $url = "/incoming/institutions/".$this->institute."/applications?formular_ids=".$id;
        $result['applications'] = $this->request($url, 'GET');
        $dataset_id = $result['applications'][0]["id"];

        $url = "/incoming/formulars/".$id."/applications/".$dataset_id."/steps";
        $result['steps'] = $this->request($url, 'GET');
        
        
        $url = "/incoming/applications/".$dataset_id."/custom_field_values";
        $result['custom_field_values'] = $this->request($url, 'GET');
        
        if (isset($result['error'])) return($result['error']);
        return $result;
    }
    
    function fields($id)
    {
        // $uri = 'formular17_2023';
        $url = "/incoming/formulars/$id/custom_fields";
        // $data = ['ids'=>$id];
        $result = $this->request($url, 'GET');

        if (isset($result['error'])) return($result['error']);

        return $result;
    }

    /**
     * Find the label of an attribute
     *
     * @param array 	$custom_field   Custom field data
     * @param array 	$step_tile      Step tile data
     * @param array  	$step           Optional step data
	 *	 
     * @return array    Returns the possible label of the attribute
     */
    function label($custom_field, $step_tile){
        $label = "";
        $labels = array();
        $caption = "";

        if ($step_tile["tile_type"] === "tabletile"){
            $cell_index = array_search($custom_field["id"], $step_tile["tablegrid"]["cells"]);
            $row_index = intval($cell_index / ($step_tile["cols"]-1));
            $header_index = $cell_index % ($step_tile["cols"]-1);
            $row = $step_tile["tablegrid"]["rows"][$row_index];
            $header = $step_tile["tablegrid"]["headers"][$header_index];
            if (!empty($row["de"]) || !empty($header["de"])) $labels[] = $row["de"]." <i>(".$header["de"].")</i>";
            if (array_key_exists("de", $custom_field["description"]) && !empty(($custom_field["description"]["de"]))) $labels[] = ($custom_field["description"]["de"]);
            if (array_key_exists("de", $step_tile["title"]) && !empty(($step_tile["title"]["de"]))) $caption = ($step_tile["title"]["de"]);
            else if (array_key_exists("de", $step_tile["description"]) && !empty(($step_tile["description"]["de"]))) $caption = ($step_tile["description"]["de"]);
        } else if ($step_tile["tile_type"] === "fieldstile"){
            if (!empty(($custom_field["title"]["de"]))) $labels[] = ($custom_field["title"]["de"]);
            if (!empty(($custom_field["description"]["de"]))) $labels[] = ($custom_field["description"]["de"]);
            if (array_key_exists("de", $step_tile["description"]) && !empty(($step_tile["description"]["de"]))) $labels[] = ($step_tile["description"]["de"]);
            if (array_key_exists("checkbox_label", $custom_field) && !empty(($custom_field["checkbox_label"]["de"]))) $labels[] = ($custom_field["checkbox_label"]["de"]);
            $caption = array_key_exists("de", $step_tile["title"]) ? ($step_tile["title"]["de"]) : "";
        }
        if (!empty($caption))
            $caption = strip_tags($caption);
        if ($this->last_title == $caption){
            $caption = "";
        } else {
            $this->last_title = $caption;
        }
        if (count($labels) == 0){
            $label = $caption;
        } else if (count($labels) == 1){
            if ($caption !== "") $label = "<b>".$caption."</b></br>";
            $label .= $labels[0];
        } else if (count($labels) > 1){
            if ($caption !== "") $label = "<b>".$caption."</b></br>";
            $label .= $labels[0] . " <i>[".implode(" -- ", array_slice($labels, 1)) . "]</i>";
        }

        return $label;
    }


    function request($url, $method = 'GET', $data = null)
    {
        $curl = curl_init();

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if (!empty($data))
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            if (!empty($data))
                $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        $header = [
            'accept: application/json',
            'Content-Type: application/json'
        ];
        if (!empty($this->token) && !empty($this->mail)){
            $header[] = "X-USER-EMAIL: ".$this->mail;
            $header[] = "X-USER-TOKEN: ".$this->token;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_URL, $this->base_url. $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if ($result === false) {
            dump(curl_error($curl));
            throw new Exception(curl_error($curl), curl_errno($curl));
        }
        curl_close($curl);
        
        $result = json_decode($result, true);
        $this->check($result);

        return $result;
    }

}
